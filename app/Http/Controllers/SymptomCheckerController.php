<?php

namespace App\Http\Controllers;

use App\Models\BodyPart;
use App\Models\Disease;
use App\Models\Doctor;
use App\Models\Symptom;
use App\Models\SymptomFactor;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;

class SymptomCheckerController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn
            ? 'লক্ষণ পরীক্ষক (Symptom Checker) - সম্ভাব্য রোগ ও বিশেষজ্ঞ ডাক্তার | DoctorBD24'
            : 'Interactive Symptom Checker - Find Potential Conditions & Doctors | DoctorBD24';
            
        $desc = $isBn
            ? 'আপনার শারীরিক লক্ষণ বা উপসর্গগুলো বাছাই করে সম্ভাব্য কারণ এবং কোন বিশেষজ্ঞ ডাক্তার দেখানো প্রয়োজন তা জেনে নিন। সহজ ও ইন্টারঅ্যাক্টিভ সিম্পটম চেকার।'
            : 'Check your symptoms with our step-by-step interactive medical checker to discover potential causes and find specialist doctors.';

        SEOTools::setTitle($title);
        SEOTools::setDescription($desc);
        SEOTools::opengraph()->setUrl(url()->current());

        $bodyParts = BodyPart::where('is_active', true)
            ->with(['symptoms' => function ($q) {
                $q->where('status', 'published')->with('factors');
            }])
            ->orderBy('order_index')
            ->get();

        return view('diseases.symptom-checker', compact('bodyParts'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'symptom_ids'   => ['required', 'array', 'min:1'],
            'symptom_ids.*' => ['exists:symptoms,id'],
            'age_group'     => ['nullable', 'in:all,adult,child'],
            'gender'        => ['nullable', 'in:all,male,female'],
            'factor_answers'=> ['nullable', 'array'],
        ]);

        $symptomIds = $request->input('symptom_ids', []);
        $factorAnswers = $request->input('factor_answers', []); // selected option IDs

        // Check if any selected factor option or symptom is a Red Flag / Emergency
        $hasEmergencyFlag = Symptom::whereIn('id', $symptomIds)->where('is_emergency', true)->exists();
        $redFlagReasons = [];

        // Check factor options for red flags
        $selectedFactors = SymptomFactor::whereIn('symptom_id', $symptomIds)->get();
        foreach ($selectedFactors as $factor) {
            if (!empty($factor->options)) {
                foreach ($factor->options as $opt) {
                    $isRed = !empty($opt['red_flag']) || !empty($opt['is_red_flag']);
                    if (in_array($opt['id'], $factorAnswers) && $isRed) {
                        $hasEmergencyFlag = true;
                        $redFlagReasons[] = [
                            'en' => $opt['label']['en'] ?? ($opt['text_en'] ?? 'Critical symptom sign detected'),
                            'bn' => $opt['label']['bn'] ?? ($opt['text_bn'] ?? 'জরুরি চিকিৎসার লক্ষণ শনাক্ত হয়েছে'),
                        ];
                    }
                }
            }
        }

        // Query matching diseases
        $diseases = Disease::where('status', 'published')
            ->whereHas('symptoms', function ($q) use ($symptomIds) {
                $q->whereIn('symptoms.id', $symptomIds);
            })
            ->with(['primarySpecialty', 'symptoms'])
            ->get();

        $rankedResults = [];

        foreach ($diseases as $disease) {
            $score = 0;
            $matchedCount = 0;
            $primaryMatched = false;

            foreach ($disease->symptoms as $sym) {
                if (in_array($sym->id, $symptomIds)) {
                    $matchedCount++;
                    $weight = (int) ($sym->pivot->match_weight ?? 10);
                    if ($sym->pivot->is_primary) {
                        $primaryMatched = true;
                        $score += ($weight * 2);
                    } else {
                        $score += $weight;
                    }
                }
            }

            // Calculate percentage match
            $totalPossible = count($symptomIds) * 20;
            $percentage = min(98, max(25, round(($score / max(1, $totalPossible)) * 100)));

            $locale = app()->getLocale();
            $rankedResults[] = [
                'id'              => $disease->id,
                'slug'            => $disease->slug,
                'name'            => [
                    'en' => $disease->getTranslation('name', 'en'),
                    'bn' => $disease->getTranslation('name', 'bn'),
                ],
                'overview'        => \Illuminate\Support\Str::limit(strip_tags($disease->getTranslation('overview', $locale)), 150),
                'specialty'       => $disease->primarySpecialty ? [
                    'id'   => $disease->primarySpecialty->id,
                    'name' => [
                        'en' => $disease->primarySpecialty->getTranslation('name', 'en'),
                        'bn' => $disease->primarySpecialty->getTranslation('name', 'bn'),
                    ],
                ] : null,
                'match_percentage'=> $percentage,
                'matched_count'   => $matchedCount,
                'primary_matched' => $primaryMatched,
                'is_emergency'    => $disease->is_emergency,
                'url'             => route(app()->getLocale() === 'bn' ? 'bn.diseases.show' : 'diseases.show', $disease->slug),
            ];
        }

        // Sort by match percentage descending
        usort($rankedResults, function ($a, $b) {
            return $b['match_percentage'] <=> $a['match_percentage'];
        });

        // Determine triage level
        $triageLevel = 'routine';
        if ($hasEmergencyFlag) {
            $triageLevel = 'emergency';
        } elseif (count($rankedResults) > 0 && ($rankedResults[0]['is_emergency'] || $rankedResults[0]['match_percentage'] >= 70)) {
            $triageLevel = 'urgent';
        }

        // Suggest Doctors
        $specialtyIds = collect($rankedResults)->pluck('specialty.id')->filter()->unique()->take(2)->all();
        $recommendedDoctors = Doctor::query()
            ->whereHas('specialties', function ($q) use ($specialtyIds) {
                if (!empty($specialtyIds)) {
                    $q->whereIn('specialties.id', $specialtyIds);
                }
            })
            ->with(['specialties', 'chambers.district'])
            ->take(4)
            ->get()
            ->map(function ($doc) {
                $locale = app()->getLocale();
                $chamber = $doc->chambers->first();
                $districtName = ($chamber && $chamber->district) ? $chamber->district->getTranslation('name', $locale) : '';
                return [
                    'name'       => $doc->getTranslation('name', $locale),
                    'slug'       => $doc->slug,
                    'specialty'  => $doc->specialties->first() ? $doc->specialties->first()->getTranslation('name', $locale) : '',
                    'location'   => $districtName,
                    'photo'      => $doc->photo ? asset('storage/' . $doc->photo) : null,
                    'url'        => route('doctors.show', $doc->slug),
                ];
            });

        return response()->json([
            'triage_level'        => $triageLevel,
            'has_emergency'       => $hasEmergencyFlag,
            'red_flag_reasons'    => $redFlagReasons,
            'results_count'       => count($rankedResults),
            'conditions'          => array_slice($rankedResults, 0, 5),
            'recommended_doctors' => $recommendedDoctors,
        ]);
    }
}
