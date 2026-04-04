<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Hospital;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Livewire\Attributes\Computed;

class HospitalList extends Component
{
    use WithPagination;

    public $urlSearch = '';

    public $search = '';

    public $hideFilters = false;
    public $seoTitle = null;
    public $seoTopContent = null;
    public $seoBottomContent = null;

    public $type = '';
    public $division = '';
    public $district = '';
    public $area = '';
    public $verified = false;

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
            'type' => ['except' => ''],
            'division' => ['except' => ''],
            'district' => ['except' => ''],
            'area' => ['except' => ''],
            'verified' => ['as' => 'verified', 'except' => false],
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
            $this->suggestions = Hospital::where('name', 'like', "%{$this->search}%")
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
        if (in_array($property, ['search', 'type', 'area', 'verified'])) {
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
        $this->reset(['search', 'urlSearch', 'type', 'division', 'district', 'area', 'verified', 'userLat', 'userLng']);
        $this->districts = [];
        $this->areas = [];
        $this->resetPage();
    }

    public function render()
    {
        $query = Hospital::with('area.district.division');

        // SEO Handling
        SEOTools::setTitle(__('Hospitals & Diagnostic Centers') . ' | DoctorBD24');
        OpenGraph::setType('website');

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->verified) {
            $query->where('verified', true);
        }

        if ($this->area) {
            $query->whereHas('area', fn($q) => $q->where('slug', $this->area));
        } elseif ($this->district) {
            $query->whereHas('area.district', fn($q) => $q->where('slug', $this->district));
        } elseif ($this->division) {
            $query->whereHas('area.district.division', fn($q) => $q->where('slug', $this->division));
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->userLat && $this->userLng) {
            $lat = (float) $this->userLat;
            $lng = (float) $this->userLng;
            $query->selectRaw("hospitals.*, ( 6371 * acos( cos( radians(?) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(?) ) + sin( radians(?) ) * sin( radians( lat ) ) ) ) AS distance", [$lat, $lng, $lat])
                  ->orderBy('distance');
        } else {
            $query->orderByDesc('featured')->orderByDesc('view_count');
        }

        $paginatedHospitals = $query->paginate(12);

        $this->mapLocations = $paginatedHospitals->getCollection()->map(function($h) {
            return [
                'id' => $h->id,
                'name' => $h->name,
                'lat' => $h->lat,
                'lng' => $h->lng,
                'address' => $h->address,
                'type' => $h->type,
                'featured' => $h->featured,
                'url' => route('hospitals.show', $h->slug)
            ];
        })->toArray();

        return view('livewire.hospital-list', [
            'hospitals' => $paginatedHospitals,
            'divisions' => Division::orderBy('id')->get(),
        ]);
    }
}
