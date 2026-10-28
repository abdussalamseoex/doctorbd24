<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\Specialty;
use App\Models\Symptom;
use App\Models\BodyPart;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\BlogPost;
use App\Models\DiseaseReference;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\ImageOptimizerService;

class AdminDiseaseController extends Controller
{
    use \App\Traits\HasBulkActions;
    protected $model = Disease::class;

    public function index(Request $request)
    {
        $baseQuery = Disease::query();

        $counts = [
            'all'       => (clone $baseQuery)->count(),
            'published' => (clone $baseQuery)->where('status', 'published')->count(),
            'draft'     => (clone $baseQuery)->where('status', 'draft')->count(),
        ];

        $query = (clone $baseQuery)->with(['primarySpecialty', 'bodySystem', 'reviewerDoctor']);

        $status = $request->query('status', 'all');
        if ($status === 'published') {
            $query->where('status', 'published');
        } elseif ($status === 'draft') {
            $query->where('status', 'draft');
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('specialty_id')) {
            $query->where('primary_specialty_id', $request->query('specialty_id'));
        }

        if ($request->filled('body_system_id')) {
            $query->where('body_system_id', $request->query('body_system_id'));
        }

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 20;
        }

        $diseases = $query->latest('id')->paginate($perPage)->withQueryString();
        $specialties = Specialty::orderBy('slug')->get();
        $bodySystems = BodyPart::orderBy('slug')->get();

        return view('admin.diseases.index', compact('diseases', 'counts', 'status', 'specialties', 'bodySystems'));
    }

    public function create()
    {
        $specialties = Specialty::orderBy('slug')->get();
        $symptoms = Symptom::with('bodyPart')->orderBy('slug')->get();
        $bodySystems = BodyPart::orderBy('slug')->get();
        $doctors = Doctor::orderBy('slug')->get();
        $hospitals = Hospital::orderBy('slug')->get();
        $articles = BlogPost::where('status', 'published')->orderBy('slug')->get();

        return view('admin.diseases.create', compact('specialties', 'symptoms', 'bodySystems', 'doctors', 'hospitals', 'articles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name.en'              => ['required', 'string', 'max:255'],
            'name.bn'              => ['nullable', 'string', 'max:255'],
            'slug'                 => ['nullable', 'string', 'max:255', 'unique:diseases,slug'],
            'primary_specialty_id' => ['nullable', 'exists:specialties,id'],
            'body_system_id'       => ['nullable', 'exists:body_parts,id'],
            'reviewer_doctor_id'   => ['nullable', 'exists:doctors,id'],
            'status'               => ['required', 'in:published,draft'],
            'is_featured'          => ['boolean'],
            'is_emergency'         => ['boolean'],
            'image'                => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $enName = $request->input('name.en');
        $bnName = $request->input('name.bn', $enName);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($enName);

        $firstLetter = strtoupper(substr($enName, 0, 1));

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageOptimizerService::storeAndOptimize($request->file('image'), 'diseases', 1200);
        }

        // Aliases handling
        $aliases = $this->parseAliases($request->input('aliases'));

        $disease = Disease::create([
            'name'                     => ['en' => $enName, 'bn' => $bnName],
            'slug'                     => $slug,
            'first_letter'             => $firstLetter,
            'primary_specialty_id'     => $request->input('primary_specialty_id'),
            'body_system_id'           => $request->input('body_system_id'),
            'reviewer_doctor_id'       => $request->input('reviewer_doctor_id'),
            'author_id'                => auth()->id(),
            'last_reviewed_at'         => $request->input('last_reviewed_at'),
            'aliases'                  => $aliases,
            'short_description'        => $request->input('short_description', []),
            'overview'                 => $request->input('overview', []),
            'symptoms_summary'         => $request->input('symptoms_summary', []),
            'symptoms_detailed'        => $request->input('symptoms_detailed', []),
            'causes'                   => $request->input('causes', []),
            'risk_factors'             => $request->input('risk_factors', []),
            'complications'            => $request->input('complications', []),
            'prevention'               => $request->input('prevention', []),
            'when_to_see_doctor'       => $request->input('when_to_see_doctor', []),
            'red_flags'                => $this->cleanRedFlags($request->input('red_flags', [])),
            'diagnosis_overview'       => $request->input('diagnosis_overview', []),
            'diagnosis_tests'          => $this->cleanRepeater($request->input('diagnosis_tests', []), ['name_en', 'name_bn']),
            'treatment_overview'       => $request->input('treatment_overview', []),
            'treatments'               => $this->cleanRepeater($request->input('treatments', []), ['name_en', 'name_bn']),
            'medicines'                => $this->cleanRepeater($request->input('medicines', []), ['name_en', 'name_bn']),
            'procedures'               => $this->cleanRepeater($request->input('procedures', []), ['name_en', 'name_bn']),
            'clinical_trials'          => $request->input('clinical_trials', []),
            'lifestyle_home_remedies'  => $request->input('lifestyle_home_remedies', []),
            'alternative_medicine'     => $request->input('alternative_medicine', []),
            'coping_support'           => $request->input('coping_support', []),
            'preparing_for_appointment'=> $request->input('preparing_for_appointment', []),
            'faqs'                     => $this->cleanRepeater($request->input('faqs', []), ['question_en', 'question_bn']),
            'diagnosis_treatment'      => $request->input('diagnosis_treatment', []),
            'image'                    => $imagePath,
            'featured_image_alt'       => $request->input('featured_image_alt', []),
            'featured_image_caption'   => $request->input('featured_image_caption', []),
            'body_images'              => $request->input('body_images', []),
            'related_conditions'       => $this->parseCommaArray($request->input('related_conditions')),
            'related_hospitals'        => array_values(array_filter(array_map('intval', (array) $request->input('related_hospitals', [])))),
            'related_articles'         => array_values(array_filter(array_map('intval', (array) $request->input('related_articles', [])))),
            'disclaimer'               => $request->input('disclaimer', []),
            'is_featured'              => $request->boolean('is_featured'),
            'is_emergency'             => $request->boolean('is_emergency'),
            'status'                   => $request->input('status', 'draft'),
            'published_at'             => $request->input('status') === 'published' ? now() : null,
        ]);

        // Sync Secondary Specialties
        if ($request->has('secondary_specialties')) {
            $disease->specialties()->sync($request->input('secondary_specialties', []));
        }

        // Sync Symptoms
        if ($request->has('symptoms')) {
            $symptomData = [];
            foreach ($request->input('symptoms', []) as $symptomId => $pivot) {
                if (!empty($pivot['selected'])) {
                    $symptomData[$symptomId] = [
                        'is_primary'   => !empty($pivot['is_primary']),
                        'match_weight' => (int) ($pivot['match_weight'] ?? 10),
                    ];
                }
            }
            $disease->symptoms()->sync($symptomData);
        }

        // Save References
        if ($request->has('references')) {
            foreach ($request->input('references', []) as $index => $ref) {
                if (!empty($ref['source_name'])) {
                    $disease->references()->create([
                        'source_name' => $ref['source_name'],
                        'source_url'  => $ref['source_url'] ?? null,
                        'notes'       => $ref['notes'] ?? null,
                        'order_index' => $index + 1,
                    ]);
                }
            }
        }

        // Save SEO Meta
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
            }
            $disease->updateSeo($seoData);
        }

        if (class_exists(\Spatie\ResponseCache\Facades\ResponseCache::class)) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }

        return redirect()->route('admin.diseases.index')->with('success', 'Disease guide created successfully.');
    }

    public function edit(Disease $disease)
    {
        $disease->load(['primarySpecialty', 'specialties', 'symptoms', 'references', 'bodySystem', 'reviewerDoctor']);
        $specialties = Specialty::orderBy('slug')->get();
        $symptoms = Symptom::with('bodyPart')->orderBy('slug')->get();
        $bodySystems = BodyPart::orderBy('slug')->get();
        $doctors = Doctor::orderBy('slug')->get();
        $hospitals = Hospital::orderBy('slug')->get();
        $articles = BlogPost::where('status', 'published')->orderBy('slug')->get();

        return view('admin.diseases.edit', compact('disease', 'specialties', 'symptoms', 'bodySystems', 'doctors', 'hospitals', 'articles'));
    }

    public function update(Request $request, Disease $disease)
    {
        $validated = $request->validate([
            'name.en'              => ['required', 'string', 'max:255'],
            'name.bn'              => ['nullable', 'string', 'max:255'],
            'slug'                 => ['nullable', 'string', 'max:255', 'unique:diseases,slug,' . $disease->id],
            'primary_specialty_id' => ['nullable', 'exists:specialties,id'],
            'body_system_id'       => ['nullable', 'exists:body_parts,id'],
            'reviewer_doctor_id'   => ['nullable', 'exists:doctors,id'],
            'status'               => ['required', 'in:published,draft'],
            'is_featured'          => ['boolean'],
            'is_emergency'         => ['boolean'],
            'image'                => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $enName = $request->input('name.en');
        $bnName = $request->input('name.bn', $enName);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($enName);

        $firstLetter = strtoupper(substr($enName, 0, 1));

        $aliases = $this->parseAliases($request->input('aliases'));

        $data = [
            'name'                     => ['en' => $enName, 'bn' => $bnName],
            'slug'                     => $slug,
            'first_letter'             => $firstLetter,
            'primary_specialty_id'     => $request->input('primary_specialty_id'),
            'body_system_id'           => $request->input('body_system_id'),
            'reviewer_doctor_id'       => $request->input('reviewer_doctor_id'),
            'last_reviewed_at'         => $request->input('last_reviewed_at'),
            'aliases'                  => $aliases,
            'short_description'        => $request->input('short_description', []),
            'overview'                 => $request->input('overview', []),
            'symptoms_summary'         => $request->input('symptoms_summary', []),
            'symptoms_detailed'        => $request->input('symptoms_detailed', []),
            'causes'                   => $request->input('causes', []),
            'risk_factors'             => $request->input('risk_factors', []),
            'complications'            => $request->input('complications', []),
            'prevention'               => $request->input('prevention', []),
            'when_to_see_doctor'       => $request->input('when_to_see_doctor', []),
            'red_flags'                => $this->cleanRedFlags($request->input('red_flags', [])),
            'diagnosis_overview'       => $request->input('diagnosis_overview', []),
            'diagnosis_tests'          => $this->cleanRepeater($request->input('diagnosis_tests', []), ['name_en', 'name_bn']),
            'treatment_overview'       => $request->input('treatment_overview', []),
            'treatments'               => $this->cleanRepeater($request->input('treatments', []), ['name_en', 'name_bn']),
            'medicines'                => $this->cleanRepeater($request->input('medicines', []), ['name_en', 'name_bn']),
            'procedures'               => $this->cleanRepeater($request->input('procedures', []), ['name_en', 'name_bn']),
            'clinical_trials'          => $request->input('clinical_trials', []),
            'lifestyle_home_remedies'  => $request->input('lifestyle_home_remedies', []),
            'alternative_medicine'     => $request->input('alternative_medicine', []),
            'coping_support'           => $request->input('coping_support', []),
            'preparing_for_appointment'=> $request->input('preparing_for_appointment', []),
            'faqs'                     => $this->cleanRepeater($request->input('faqs', []), ['question_en', 'question_bn']),
            'diagnosis_treatment'      => $request->input('diagnosis_treatment', []),
            'featured_image_alt'       => $request->input('featured_image_alt', []),
            'featured_image_caption'   => $request->input('featured_image_caption', []),
            'body_images'              => $request->input('body_images', []),
            'related_conditions'       => $this->parseCommaArray($request->input('related_conditions')),
            'related_hospitals'        => array_values(array_filter(array_map('intval', (array) $request->input('related_hospitals', [])))),
            'related_articles'         => array_values(array_filter(array_map('intval', (array) $request->input('related_articles', [])))),
            'disclaimer'               => $request->input('disclaimer', []),
            'is_featured'              => $request->boolean('is_featured'),
            'is_emergency'             => $request->boolean('is_emergency'),
            'status'                   => $request->input('status', 'draft'),
        ];

        if ($request->input('status') === 'published' && !$disease->published_at) {
            $data['published_at'] = now();
        }

        if ($request->boolean('remove_image')) {
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            $data['image'] = ImageOptimizerService::storeAndOptimize($request->file('image'), 'diseases', 1200);
        }

        $disease->update($data);

        // Sync Secondary Specialties
        $disease->specialties()->sync($request->input('secondary_specialties', []));

        // Sync Symptoms
        $symptomData = [];
        if ($request->has('symptoms')) {
            foreach ($request->input('symptoms', []) as $symptomId => $pivot) {
                if (!empty($pivot['selected'])) {
                    $symptomData[$symptomId] = [
                        'is_primary'   => !empty($pivot['is_primary']),
                        'match_weight' => (int) ($pivot['match_weight'] ?? 10),
                    ];
                }
            }
        }
        $disease->symptoms()->sync($symptomData);

        // Replace References
        $disease->references()->delete();
        if ($request->has('references')) {
            foreach ($request->input('references', []) as $index => $ref) {
                if (!empty($ref['source_name'])) {
                    $disease->references()->create([
                        'source_name' => $ref['source_name'],
                        'source_url'  => $ref['source_url'] ?? null,
                        'notes'       => $ref['notes'] ?? null,
                        'order_index' => $index + 1,
                    ]);
                }
            }
        }

        // Save SEO Meta
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
            }
            $disease->updateSeo($seoData);
        }

        if (class_exists(\Spatie\ResponseCache\Facades\ResponseCache::class)) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }

        return redirect()->route('admin.diseases.index')->with('success', 'Disease guide updated successfully.');
    }

    public function destroy(Disease $disease)
    {
        $disease->delete();

        if (class_exists(\Spatie\ResponseCache\Facades\ResponseCache::class)) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }

        return redirect()->route('admin.diseases.index')->with('success', 'Disease deleted successfully.');
    }

    private function parseAliases($input): array
    {
        if (is_array($input)) {
            return array_values(array_filter(array_map('trim', $input)));
        }
        if (is_string($input)) {
            $parts = explode(',', $input);
            return array_values(array_filter(array_map('trim', $parts)));
        }
        return [];
    }

    private function parseCommaArray($input): array
    {
        if (is_array($input)) {
            return array_values(array_filter(array_map('trim', $input)));
        }
        if (is_string($input)) {
            $parts = preg_split('/[\r\n,]+/', $input);
            return array_values(array_filter(array_map('trim', $parts)));
        }
        return [];
    }

    private function cleanRedFlags($flags): array
    {
        if (!is_array($flags)) return [];
        $cleaned = [];
        foreach ($flags as $flag) {
            if (is_array($flag)) {
                $en = trim($flag['en'] ?? '');
                $bn = trim($flag['bn'] ?? '');
                if ($en !== '' || $bn !== '') {
                    $cleaned[] = ['en' => $en, 'bn' => $bn ?: $en];
                }
            } elseif (is_string($flag) && trim($flag) !== '') {
                $cleaned[] = ['en' => trim($flag), 'bn' => trim($flag)];
            }
        }
        return $cleaned;
    }

    private function cleanRepeater($items, array $checkKeys): array
    {
        if (!is_array($items)) return [];
        $cleaned = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $hasContent = false;
            foreach ($checkKeys as $k) {
                if (!empty(trim((string)($item[$k] ?? '')))) {
                    $hasContent = true;
                    break;
                }
            }
            if ($hasContent) {
                $cleaned[] = $item;
            }
        }
        return $cleaned;
    }
}
