<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Symptom;
use App\Models\BodyPart;
use App\Models\SymptomFactor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSymptomController extends Controller
{
    use \App\Traits\HasBulkActions;
    protected $model = Symptom::class;

    public function index(Request $request)
    {
        $baseQuery = Symptom::query();

        $counts = [
            'all'       => (clone $baseQuery)->count(),
            'emergency' => (clone $baseQuery)->where('is_emergency', true)->count(),
            'routine'   => (clone $baseQuery)->where('urgency_level', 'routine')->count(),
        ];

        $query = (clone $baseQuery)->with(['bodyPart', 'factors']);

        if ($request->filled('body_part_id')) {
            $query->where('body_part_id', $request->query('body_part_id'));
        }

        if ($request->filled('urgency_level')) {
            $query->where('urgency_level', $request->query('urgency_level'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 20;
        }

        $symptoms = $query->latest('id')->paginate($perPage)->withQueryString();
        $bodyParts = BodyPart::orderBy('order_index')->get();

        return view('admin.symptoms.index', compact('symptoms', 'counts', 'bodyParts'));
    }

    public function create()
    {
        $bodyParts = BodyPart::orderBy('order_index')->get();
        return view('admin.symptoms.create', compact('bodyParts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name.en'       => ['required', 'string', 'max:255'],
            'name.bn'       => ['nullable', 'string', 'max:255'],
            'slug'          => ['nullable', 'string', 'max:255', 'unique:symptoms,slug'],
            'body_part_id'  => ['nullable', 'exists:body_parts,id'],
            'urgency_level' => ['required', 'in:emergency,urgent,routine'],
            'gender_scope'  => ['required', 'in:all,male,female'],
            'age_group'     => ['required', 'in:all,adult,child'],
            'is_emergency'  => ['boolean'],
            'is_featured'   => ['boolean'],
        ]);

        $enName = $request->input('name.en');
        $bnName = $request->input('name.bn', $enName);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($enName);

        $firstLetter = strtoupper(substr($enName, 0, 1));

        $symptom = Symptom::create([
            'body_part_id'  => $request->input('body_part_id'),
            'name'          => ['en' => $enName, 'bn' => $bnName],
            'slug'          => $slug,
            'first_letter'  => $firstLetter,
            'description'   => $request->input('description', []),
            'gender_scope'  => $request->input('gender_scope', 'all'),
            'age_group'     => $request->input('age_group', 'all'),
            'urgency_level' => $request->input('urgency_level', 'routine'),
            'is_emergency'  => $request->boolean('is_emergency') || $request->input('urgency_level') === 'emergency',
            'is_featured'   => $request->boolean('is_featured'),
            'status'        => 'published',
        ]);

        // Process Factors
        if ($request->has('factors')) {
            foreach ($request->input('factors', []) as $index => $factor) {
                if (!empty($factor['question']['en']) || !empty($factor['question']['bn'])) {
                    $options = [];
                    if (!empty($factor['options'])) {
                        foreach ($factor['options'] as $opt) {
                            if (!empty($opt['text_en']) || !empty($opt['text_bn'])) {
                                $options[] = [
                                    'id'       => Str::slug($opt['text_en'] ?? $opt['text_bn']),
                                    'text_en'  => $opt['text_en'] ?? '',
                                    'text_bn'  => $opt['text_bn'] ?? ($opt['text_en'] ?? ''),
                                    'red_flag' => !empty($opt['red_flag']),
                                    'score'    => (int) ($opt['score'] ?? 5),
                                ];
                            }
                        }
                    }

                    $symptom->factors()->create([
                        'factor_type' => $factor['factor_type'] ?? 'quality',
                        'question'    => [
                            'en' => $factor['question']['en'] ?? '',
                            'bn' => $factor['question']['bn'] ?? ($factor['question']['en'] ?? ''),
                        ],
                        'options'     => $options,
                        'order_index' => $index + 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.symptoms.index')->with('success', 'Symptom created successfully.');
    }

    public function edit(Symptom $symptom)
    {
        $symptom->load(['bodyPart', 'factors']);
        $bodyParts = BodyPart::orderBy('order_index')->get();

        return view('admin.symptoms.edit', compact('symptom', 'bodyParts'));
    }

    public function update(Request $request, Symptom $symptom)
    {
        $validated = $request->validate([
            'name.en'       => ['required', 'string', 'max:255'],
            'name.bn'       => ['nullable', 'string', 'max:255'],
            'slug'          => ['nullable', 'string', 'max:255', 'unique:symptoms,slug,' . $symptom->id],
            'body_part_id'  => ['nullable', 'exists:body_parts,id'],
            'urgency_level' => ['required', 'in:emergency,urgent,routine'],
            'gender_scope'  => ['required', 'in:all,male,female'],
            'age_group'     => ['required', 'in:all,adult,child'],
            'is_emergency'  => ['boolean'],
            'is_featured'   => ['boolean'],
        ]);

        $enName = $request->input('name.en');
        $bnName = $request->input('name.bn', $enName);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($enName);

        $firstLetter = strtoupper(substr($enName, 0, 1));

        $symptom->update([
            'body_part_id'  => $request->input('body_part_id'),
            'name'          => ['en' => $enName, 'bn' => $bnName],
            'slug'          => $slug,
            'first_letter'  => $firstLetter,
            'description'   => $request->input('description', []),
            'gender_scope'  => $request->input('gender_scope', 'all'),
            'age_group'     => $request->input('age_group', 'all'),
            'urgency_level' => $request->input('urgency_level', 'routine'),
            'is_emergency'  => $request->boolean('is_emergency') || $request->input('urgency_level') === 'emergency',
            'is_featured'   => $request->boolean('is_featured'),
        ]);

        // Replace Factors
        $symptom->factors()->delete();
        if ($request->has('factors')) {
            foreach ($request->input('factors', []) as $index => $factor) {
                if (!empty($factor['question']['en']) || !empty($factor['question']['bn'])) {
                    $options = [];
                    if (!empty($factor['options'])) {
                        foreach ($factor['options'] as $opt) {
                            if (!empty($opt['text_en']) || !empty($opt['text_bn'])) {
                                $options[] = [
                                    'id'       => Str::slug($opt['text_en'] ?? $opt['text_bn']),
                                    'text_en'  => $opt['text_en'] ?? '',
                                    'text_bn'  => $opt['text_bn'] ?? ($opt['text_en'] ?? ''),
                                    'red_flag' => !empty($opt['red_flag']),
                                    'score'    => (int) ($opt['score'] ?? 5),
                                ];
                            }
                        }
                    }

                    $symptom->factors()->create([
                        'factor_type' => $factor['factor_type'] ?? 'quality',
                        'question'    => [
                            'en' => $factor['question']['en'] ?? '',
                            'bn' => $factor['question']['bn'] ?? ($factor['question']['en'] ?? ''),
                        ],
                        'options'     => $options,
                        'order_index' => $index + 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.symptoms.index')->with('success', 'Symptom updated successfully.');
    }

    public function destroy(Symptom $symptom)
    {
        $symptom->delete();
        return redirect()->route('admin.symptoms.index')->with('success', 'Symptom deleted successfully.');
    }
}
