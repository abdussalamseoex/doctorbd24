<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Ambulance;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Livewire\Attributes\Computed;

class AmbulanceList extends Component
{
    use WithPagination;

    public $urlSearch = '';

    public $search = '';

    public $hideFilters = false;
    public $seoTitle = null;
    public $seoTopContent = null;
    public $seoBottomContent = null;

    public $fixedType = null;

    public $type = '';
    public $division = '';
    public $district = '';
    public $area = '';
    public $available24h = false;

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
            'urlSearch' => ['as' => 'q', 'except' => ''],
            'type' => ['except' => ''],
            'division' => ['except' => ''],
            'district' => ['except' => ''],
            'area' => ['except' => ''],
            'available24h' => ['as' => 'available_24h', 'except' => false],
        ];
    }

    public $districts = [];
    public $areas = [];
    public $suggestions = [];
    public $showSuggestions = false;

    public function mount($fixedType = null)
    {
        if ($fixedType) {
            $this->fixedType = $fixedType;
        }

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
            $this->suggestions = Ambulance::where('provider_name', 'like', "%{$this->search}%")
                ->limit(5)
                ->pluck('provider_name')
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
        if (in_array($property, ['type', 'area', 'available24h'])) {
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
        $this->reset(['search', 'urlSearch', 'division', 'district', 'area', 'available24h', 'userLat', 'userLng']);
        if (!$this->fixedType) {
            $this->type = '';
        }
        $this->districts = [];
        $this->areas = [];
        $this->resetPage();
    }

    public function getSelectedTypeProperty()
    {
        if ($this->fixedType) {
            return $this->fixedType;
        }
        
        if ($this->type && $this->type !== '') {
            return \App\Models\AmbulanceType::where('slug', $this->type)->first();
        }
        
        return null;
    }

    public function render()
    {
        $query = Ambulance::with('area.district')->where('active', true);

        // SEO Handling
        if ($this->fixedType) {
            SEOTools::setTitle($this->fixedType->name . ' | DoctorBD24');
        } else {
            SEOTools::setTitle(__('Ambulance Service') . ' | DoctorBD24');
        }
        OpenGraph::setType('website');

        if ($this->fixedType) {
            $query->whereJsonContains('type', $this->fixedType->slug);
        } elseif ($this->type) {
            $query->whereJsonContains('type', $this->type);
        }

        if ($this->available24h) {
            $query->where('available_24h', true);
        }

        if ($this->area) {
            $query->whereHas('area', fn($q) => $q->where('slug', $this->area));
        } elseif ($this->district) {
            $query->whereHas('area.district', fn($q) => $q->where('slug', $this->district));
        } elseif ($this->division) {
            $query->whereHas('area.district.division', fn($q) => $q->where('slug', $this->division));
        }
        if ($this->search) {
            $query->where('provider_name', 'like', '%' . $this->search . '%');
        }

        if ($this->userLat && $this->userLng) {
            $lat = (float) $this->userLat;
            $lng = (float) $this->userLng;
            $query->selectRaw("ambulances.*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance", [$lat, $lng, $lat])
                  ->orderBy('distance');
        } else {
            $query->orderBy('is_featured', 'desc')->orderBy('view_count', 'desc');
        }

        $ambulances = $query->paginate(10);

        $this->mapLocations = $ambulances->getCollection()->map(function($a) {
            return [
                'id' => $a->id,
                'name' => $a->provider_name,
                'lat' => $a->latitude,
                'lng' => $a->longitude,
                'address' => $a->address,
                'type' => implode(', ', $a->getTypeLabelsArray()),
                'featured' => $a->is_featured,
                'url' => route('ambulances.show', $a->slug)
            ];
        })->toArray();

        return view('livewire.ambulance-list', [
            'ambulances' => $ambulances,
            'divisions'  => Division::orderBy('id')->get(),
        ]);
    }
}
