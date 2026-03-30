<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use App\Models\Hospital;
use Artesaos\SEOTools\Facades\SEOTools;

class DoctorList extends Component
{
    use WithPagination;

    public $urlSearch = '';
    public $search = '';
    public $hideFilters = false;
    public $seoTitle = null;
    public $seoTopContent = null;
    public $seoBottomContent = null;

    public $specialty = '';
    public $division = '';
    public $district = '';
    public $area = '';
    public $gender = '';
    public $minExperience = '';
    public $sort = 'latest';

    public function queryString()
    {
        if ($this->hideFilters) {
            return [];
        }

        return [
            'urlSearch' => ['as' => 'search', 'except' => ''],
            'specialty' => ['except' => ''],
            'division' => ['except' => ''],
            'district' => ['except' => ''],
            'area' => ['except' => ''],
            'sort' => ['except' => 'latest'],
        ];
    }

    public $districts = [];
    public $areas = [];
    public $suggestions = [];
    public $showSuggestions = false;

    public function mount()
    {
        $this->search = $this->urlSearch;
        if ($this->division) {
            $this->loadDistricts();
        }
        if ($this->district) {
            $this->loadAreas();
        }
    }

    public function updatedDivision()
    {
        $this->district = '';
        $this->area = '';
        $this->loadDistricts();
        $this->resetPage();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->suggestions = Doctor::where('name', 'like', "%{$this->search}%")
                ->limit(5)
                ->pluck('name')
                ->toArray();
            $this->showSuggestions = true;
        } else {
            $this->suggestions = [];
            $this->showSuggestions = false;
        }
        $this->resetPage();
    }

    public function selectSuggestion($name)
    {
        $this->search = $name;
        $this->urlSearch = $name;
        $this->showSuggestions = false;
        $this->resetPage();
    }

    public function applySearch()
    {
        $this->urlSearch = $this->search;
        $this->showSuggestions = false;
        $this->resetPage();
    }

    public function updatedDistrict()
    {
        $this->area = '';
        $this->loadAreas();
        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'specialty', 'area', 'gender', 'minExperience', 'sort'])) {
            $this->resetPage();
        }
    }

    public function loadDistricts()
    {
        if ($this->division) {
            $div = Division::where('slug', $this->division)->first();
            $this->districts = $div ? District::where('division_id', $div->id)->get() : [];
        } else {
            $this->districts = [];
        }
    }

    public function loadAreas()
    {
        if ($this->district) {
            $dist = District::where('slug', $this->district)->first();
            $this->areas = $dist ? Area::where('district_id', $dist->id)->get() : [];
        } else {
            $this->areas = [];
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'urlSearch', 'specialty', 'division', 'district', 'area', 'gender', 'minExperience', 'sort']);
        $this->districts = [];
        $this->areas = [];
        $this->resetPage();
    }

    public function render()
    {
        $query = Doctor::with(['specialties', 'chambers.area.district.division', 'reviews']);

        // SEO Handling
        SEOTools::setTitle(__('Find Doctor') . ' | DoctorBD24');
        SEOTools::setDescription('বাংলাদেশের সেরা বিশেষজ্ঞ ডাক্তারদের তালিকা। এলাকা ও বিশেষজ্ঞতা অনুযায়ী সহজে খুঁজুন।');

        if ($this->search) {
            $query->where(function (\Illuminate\Database\Eloquent\Builder $q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('about', 'like', "%{$this->search}%");
            });
        }

        if ($this->specialty) {
            $query->whereHas('specialties', fn($q) => $q->where('slug', $this->specialty));
        }

        if ($this->gender) {
            $query->where('gender', $this->gender);
        }

        if ($this->minExperience) {
            $query->where('experience_years', '>=', (int)$this->minExperience);
        }

        if ($this->area) {
            $query->whereHas('chambers.area', fn($q) => $q->where('slug', $this->area));
        } elseif ($this->district) {
            $query->whereHas('chambers.area.district', fn($q) => $q->where('slug', $this->district));
        } elseif ($this->division) {
            $query->whereHas('chambers.area.district.division', fn($q) => $q->where('slug', $this->division));
        }

        switch ($this->sort) {
            case 'experience': $query->orderByDesc('experience_years'); break;
            case 'rating':     $query->orderByDesc('rating_avg'); break;
            case 'fees_low':   $query->orderBy('fee_range_min'); break;
            default:           $query->latest(); break;
        }

        return view('livewire.doctor-list', [
            'doctors'     => $query->paginate(12),
            'specialties' => Specialty::orderBy('name->' . app()->getLocale())->get(),
            'divisions'   => Division::orderBy('id')->get(),
        ]);
    }
}
