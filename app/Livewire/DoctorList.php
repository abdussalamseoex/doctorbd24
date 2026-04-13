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
use Livewire\Attributes\Computed;

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

    public $userLat = null;
    public $userLng = null;
    public $showMapView = false;
    public $mapLocations = [];

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
            $this->suggestions = Doctor::published()
                ->where('name', 'like', "%{$this->search}%")
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
        $this->reset(['search', 'urlSearch', 'specialty', 'division', 'district', 'area', 'gender', 'minExperience', 'sort', 'userLat', 'userLng']);
        $this->districts = [];
        $this->areas = [];
        $this->resetPage();
    }

    public function render()
    {
        $query = Doctor::published()->with(['specialties', 'chambers.area.district.division', 'reviews']);

        // SEO Handling
        if (!$this->seoTitle) {
            SEOTools::setTitle(__('Find Doctor') . ' | DoctorBD24');
            SEOTools::setDescription('বাংলাদেশের সেরা বিশেষজ্ঞ ডাক্তারদের তালিকা। এলাকা ও বিশেষজ্ঞতা অনুযায়ী সহজে খুঁজুন।');
        }

        if ($this->search) {
            $searchTerm = "%{$this->search}%";
            $query->where(function (\Illuminate\Database\Eloquent\Builder $q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('bio', 'like', $searchTerm)
                  ->orWhereHas('specialties', function ($sq) use ($searchTerm) {
                      $sq->where('name', 'like', $searchTerm);
                  });
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

        if ($this->userLat && $this->userLng) {
            $lat = (float) $this->userLat;
            $lng = (float) $this->userLng;
            $query->selectRaw("doctors.*, (SELECT MIN(6371 * acos( cos( radians(?) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(?) ) + sin( radians(?) ) * sin( radians( lat ) ) )) FROM chambers WHERE chambers.doctor_id = doctors.id AND chambers.lat IS NOT NULL) AS distance", [$lat, $lng, $lat])
                  ->orderBy('distance');
        } else {
            switch ($this->sort) {
                case 'experience': $query->orderByDesc('experience_years'); break;
                case 'rating':     $query->orderByDesc('rating_avg'); break;
                case 'fees_low':   $query->orderBy('fee_range_min'); break;
                default:           $query->latest(); break;
            }
        }

        $paginatedDoctors = $query->paginate(12);

        $this->mapLocations = $paginatedDoctors->getCollection()->map(function($d) {
            $chamber = $d->chambers->firstWhere(fn($c) => $c->lat && $c->lng);
            if (!$chamber) return null;
            return [
                'id' => $d->id,
                'name' => $d->name,
                'lat' => $chamber->lat,
                'lng' => $chamber->lng,
                'address' => $chamber->address,
                'type' => $d->specialties->pluck('name')->first() ?: 'Specialist',
                'featured' => $d->featured,
                'url' => route('doctors.show', $d->slug)
            ];
        })->filter()->values()->toArray();

        return view('livewire.doctor-list', [
            'doctors'     => $paginatedDoctors,
            'specialties' => Specialty::orderBy('name->' . app()->getLocale())->get(),
            'divisions'   => Division::orderBy('id')->get(),
        ]);
    }
}
