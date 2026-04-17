<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\HospitalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminHospitalServiceController extends Controller
{
    public function index(Hospital $hospital)
    {
        $services = $hospital->hospitalServices()->orderBy('service_category')->orderBy('service_name')->paginate(50);
        return view('admin.hospital_services.index', compact('hospital', 'services'));
    }

    public function store(Request $request, Hospital $hospital)
    {
        $request->validate([
            'service_category' => 'nullable|string|max:255',
            'service_name'     => 'required|array',
            'service_name.en'  => 'required|string|max:500',
            'description'      => 'nullable|array',
            'price'            => 'nullable|string|max:255',
            'is_active'        => 'boolean',
        ]);

        $hospital->hospitalServices()->create([
            'service_category' => $request->service_category,
            'service_name'     => $request->service_name,
            'description'      => $request->description,
            'price'            => $request->price,
            'is_active'        => $request->has('is_active') ? $request->is_active : true,
        ]);

        return back()->with('success', 'Service added successfully.');
    }

    public function update(Request $request, Hospital $hospital, HospitalService $service)
    {
        if ($service->hospital_id !== $hospital->id) {
            abort(403);
        }

        $request->validate([
            'service_category' => 'nullable|string|max:255',
            'service_name'     => 'required|array',
            'service_name.en'  => 'required|string|max:500',
            'description'      => 'nullable|array',
            'price'            => 'nullable|string|max:255',
            'is_active'        => 'boolean',
        ]);

        $service->update([
            'service_category' => $request->service_category,
            'service_name'     => $request->service_name,
            'description'      => $request->description,
            'price'            => $request->price,
            'is_active'        => $request->has('is_active') ? $request->is_active : true,
        ]);

        return back()->with('success', 'Service updated successfully.');
    }

    public function destroy(Hospital $hospital, HospitalService $service)
    {
        if ($service->hospital_id !== $hospital->id) {
            abort(403);
        }
        $service->delete();
        return back()->with('success', 'Service deleted successfully.');
    }

    public function clearAll(Hospital $hospital)
    {
        $hospital->hospitalServices()->delete();
        return back()->with('success', 'All services cleared for this hospital.');
    }

    public function import(Request $request, Hospital $hospital)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        try {
            $file = $request->file('csv_file');
            $handle = fopen($file->getRealPath(), "r");
            
            $header = fgetcsv($handle, 10000, ",");
            if (!$header) {
                return back()->with('error', 'Invalid CSV file format.');
            }

            // Normalize headers
            $header = array_map(function($val) {
                return strtolower(trim(preg_replace('/[^A-Za-z0-9_]/', '', $val)));
            }, $header);

            // Find column indexes
            $nameIdx = -1;
            $catIdx = -1;
            $priceIdx = -1;
            $descIdx = -1;

            foreach($header as $idx => $col) {
                if (str_contains($col, 'name')) $nameIdx = $idx;
                if (str_contains($col, 'categor')) $catIdx = $idx;
                if (str_contains($col, 'price') || str_contains($col, 'charge') || str_contains($col, 'tk')) $priceIdx = $idx;
                if (str_contains($col, 'desc') || str_contains($col, 'detail')) $descIdx = $idx;
            }

            if ($nameIdx === -1 || $priceIdx === -1) {
                // Try fallback based on standard 4 column: SL, Category, Name, Price
                if (count($header) >= 4) {
                    $catIdx = 1;
                    $nameIdx = 2;
                    $priceIdx = 3;
                } else {
                    return back()->with('error', 'Could not detect Name and Price columns. Please use a standard CSV structure.');
                }
            }

            $currentCategory = null;
            $currentName = null;
            $servicesToInsert = [];

            DB::beginTransaction();

            // Clear old if requested
            if ($request->has('clear_old')) {
                $hospital->hospitalServices()->delete();
            }

            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                // Handle alternating empty rows like SL, , , Price (used as urgent price or just weird PDF copy)
                $cat = isset($data[$catIdx]) ? trim($data[$catIdx]) : '';
                $name = isset($data[$nameIdx]) ? trim($data[$nameIdx]) : '';
                $price = isset($data[$priceIdx]) ? trim($data[$priceIdx]) : '';

                $desc = ($descIdx !== -1 && isset($data[$descIdx])) ? trim($data[$descIdx]) : null;

                if (empty($cat) && empty($name) && empty($price) && empty($desc)) {
                    continue; // Completely empty row
                }

                // If row has no name but has a price, we assume it belongs to the PREVIOUS item 
                // We'll append the price to the previous item.
                if (empty($name) && !empty($price)) {
                    if (count($servicesToInsert) > 0) {
                        $lastIdx = count($servicesToInsert) - 1;
                        if (!str_contains($servicesToInsert[$lastIdx]['price'], $price)) {
                            // Format: "Norm: 500 | Adv: 700" etc.
                            $servicesToInsert[$lastIdx]['price'] .= ' / ' . $price;
                        }
                    }
                    continue;
                }

                if (!empty($cat)) {
                    $currentCategory = $cat; // Remember last seen category
                }

                if (!empty($name)) {
                    // Must generate a unique slug just in case
                    $baseSlug = Str::slug($name);
                    $rand = Str::random(5);

                    $servicesToInsert[] = [
                        'hospital_id' => $hospital->id,
                        'service_category' => Str::limit($currentCategory ?? 'Uncategorized', 250),
                        'service_name' => Str::limit($name, 490),
                        'slug' => $baseSlug . '-' . $rand,
                        'description' => $desc,
                        'price' => Str::limit($price, 250),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            fclose($handle);

            // Chunk insert
            $chunks = array_chunk($servicesToInsert, 500);
            foreach ($chunks as $chunk) {
                HospitalService::insert($chunk);
            }

            DB::commit();
            return back()->with('success', count($servicesToInsert) . ' services imported successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hospital Service Import Error: ' . $e->getMessage());
            return back()->with('error', 'Error during import: ' . $e->getMessage());
        }
    }

    public function generateBiDescription(Request $request, Hospital $hospital)
    {
        $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'integer|exists:hospital_services,id'
        ]);

        // Get AI settings
        $provider = \App\Models\Setting::get('ai_provider', 'openai');
        $apiKey = '';
        if ($provider === 'openai' || $provider === 'custom_openai') {
            $apiKey = \App\Models\Setting::get('openai_api_key');
        } else {
            $apiKey = \App\Models\Setting::get('gemini_api_key');
        }

        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'AI API Key is missing.'], 400);
        }

        $targetLang = $request->input('target_language', 'both'); // en, bn, both
        $results = [];
        $services = HospitalService::whereIn('id', $request->service_ids)->where('hospital_id', $hospital->id)->get();

        foreach($services as $service) {
            $descEn = $service->getTranslation('description', 'en', false);
            $descBn = $service->getTranslation('description', 'bn', false);
            
            $generateEn = ($targetLang === 'en' || $targetLang === 'both');
            $generateBn = ($targetLang === 'bn' || $targetLang === 'both');

            try {
                // 1. Generate English if needed
                if ($generateEn && empty($descEn)) {
                    $serviceNameEn = $service->getTranslation('service_name', 'en', false) ?: 'Diagnostic Test';
                    $promptEn = "Write a highly accurate, 2-3 sentence medical description for a diagnostic test/service named '{$serviceNameEn}'. This description is for patients to understand what the test involves. Do NOT use markdown. Do NOT use html tags except paragraph <p>. Tone: Informative and Professional.";
                    
                    if ($provider === 'gemini') {
                        $resEn = \Illuminate\Support\Facades\Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
                            'contents' => [['parts' => [['text' => "System: You are a medical professional.\n\nUser: " . $promptEn]]]],
                            'generationConfig' => ['temperature' => 0.6]
                        ]);
                        if ($resEn->successful()) $descEn = trim($resEn->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');
                    } else {
                        $baseUrl = ($provider === 'custom_openai' && \App\Models\Setting::get('openai_base_url')) 
                                  ? rtrim(preg_replace('#/chat/completions/?$#i', '', \App\Models\Setting::get('openai_base_url')), '/') : 'https://api.openai.com/v1';
                        $model = ($provider === 'custom_openai' && \App\Models\Setting::get('openai_model')) ? \App\Models\Setting::get('openai_model') : 'gpt-4o-mini';
                        
                        $resEn = \Illuminate\Support\Facades\Http::withToken($apiKey)->post($baseUrl . '/chat/completions', [
                            'model' => $model,
                            'messages' => [
                                ['role' => 'system', 'content' => 'You are a medical professional writing for patients.'],
                                ['role' => 'user', 'content' => $promptEn]
                            ], 'temperature' => 0.6
                        ]);
                        if ($resEn->successful()) $descEn = trim($resEn->json()['choices'][0]['message']['content'] ?? '');
                    }
                }

                // 2. Generate Bengali if needed
                $nameBn = $service->getTranslation('service_name', 'bn', false);
                if ($generateBn && (empty($descBn) || empty($nameBn))) {
                    $serviceNameEn = $service->getTranslation('service_name', 'en', false) ?: 'Diagnostic Test';
                    $contextData = $descEn ?: 'Standard medical diagnostic test.';
                    
                    $promptBn = "You are a medical translator in Bangladesh. Respond ONLY with a valid JSON.
RULE FOR NAME: Phonetically transliterate the English test name into Bengali letters (e.g. 'CBC Test' -> 'সিবিসি টেস্ট', 'Ambulance' -> 'অ্যাম্বুলেন্স'). Do NOT use pure dictionary words for names.
RULE FOR DESCRIPTION: Translate the description into simple, conversational Bengali for ordinary patients.

Test Name: {$serviceNameEn}
Description: {$contextData}

Return JSON strictly in this format:
{
  \"name\": \"...\",
  \"description\": \"...\"
}";
                    
                    $jsonResult = null;
                    if ($provider === 'gemini') {
                        $resBn = \Illuminate\Support\Facades\Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
                            'contents' => [['parts' => [['text' => $promptBn]]]],
                            'generationConfig' => ['temperature' => 0.4]
                        ]);
                        if ($resBn->successful()) $jsonResult = trim($resBn->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');
                    } else {
                        $baseUrl = ($provider === 'custom_openai' && \App\Models\Setting::get('openai_base_url')) 
                                  ? rtrim(preg_replace('#/chat/completions/?$#i', '', \App\Models\Setting::get('openai_base_url')), '/') : 'https://api.openai.com/v1';
                        $model = ($provider === 'custom_openai' && \App\Models\Setting::get('openai_model')) ? \App\Models\Setting::get('openai_model') : 'gpt-4o-mini';
                        
                        $resBn = \Illuminate\Support\Facades\Http::withToken($apiKey)->post($baseUrl . '/chat/completions', [
                            'model' => $model,
                            'messages' => [['role' => 'user', 'content' => $promptBn]], 
                            'temperature' => 0.4
                        ]);
                        if ($resBn->successful()) $jsonResult = trim($resBn->json()['choices'][0]['message']['content'] ?? '');
                    }

                    if ($jsonResult) {
                        // Strip markdown formatting if any
                        $jsonResult = preg_replace('/```json/i', '', $jsonResult);
                        $jsonResult = preg_replace('/```/', '', $jsonResult);
                        $decoded = json_decode(trim($jsonResult), true);
                        if ($decoded && is_array($decoded)) {
                            if (empty($nameBn) && !empty($decoded['name'])) $nameBn = $decoded['name'];
                            if (empty($descBn) && !empty($decoded['description'])) $descBn = $decoded['description'];
                        }
                    }
                }

                // Append both to model
                if ($descEn || $descBn || $nameBn) {
                    $existingDesc = $service->getTranslations('description') ?: [];
                    $existingName = $service->getTranslations('service_name') ?: [];
                    
                    if ($descEn) $existingDesc['en'] = $descEn;
                    if ($descBn) $existingDesc['bn'] = $descBn;
                    if ($nameBn) $existingName['bn'] = $nameBn;
                    
                    $service->update([
                        'description' => $existingDesc,
                        'service_name' => $existingName
                    ]);
                }
                
                $results[] = ['id' => $service->id, 'status' => 'success'];

            } catch (\Exception $e) {
                $results[] = ['id' => $service->id, 'status' => 'failed', 'reason' => $e->getMessage()];
            }
        }

        return response()->json(['success' => true, 'results' => $results]);
    }
}
