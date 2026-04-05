@props(['showHospital' => false, 'hospitals' => []])

<div x-data="adminLocationFilter({{ request('division_id', 'null') }}, {{ request('district_id', 'null') }}, {{ request('area_id', 'null') }})" class="flex flex-wrap items-center gap-2">
    <!-- Division -->
    <select name="division_id" x-model="divisionId" @change="fetchDistricts" class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
        <option value="">All Divisions</option>
        @foreach(\App\Models\Division::orderBy('name')->get() as $div)
            <option value="{{ $div->id }}">{{ $div->name }}</option>
        @endforeach
    </select>

    <!-- District -->
    <select name="district_id" x-model="districtId" @change="fetchAreas" x-show="districts.length > 0 || divisionId" x-cloak class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
        <option value="">All Districts</option>
        <template x-for="dist in districts" :key="dist.id">
            <option :value="dist.id" x-text="dist.name" :selected="dist.id == districtId"></option>
        </template>
    </select>

    <!-- Area -->
    <select name="area_id" x-model="areaId" x-show="areas.length > 0 || districtId" x-cloak class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
        <option value="">All Areas</option>
        <template x-for="area in areas" :key="area.id">
            <option :value="area.id" x-text="area.name" :selected="area.id == areaId"></option>
        </template>
    </select>

    <!-- Hospital -->
    @if($showHospital)
    <select name="hospital_id" class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 max-w-[200px] truncate">
        <option value="">All Hospitals</option>
        @foreach($hospitals as $hosp)
            <option value="{{ $hosp->id }}" {{ request('hospital_id') == $hosp->id ? 'selected' : '' }}>{{ $hosp->name }}</option>
        @endforeach
    </select>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminLocationFilter', (initialDiv, initialDist, initialArea) => ({
                divisionId: initialDiv || '',
                districtId: initialDist || '',
                areaId: initialArea || '',
                districts: [],
                areas: [],
                
                init() {
                    if (this.divisionId) this.fetchDistricts(false);
                    if (this.districtId) this.fetchAreas(false);
                },

                fetchDistricts(resetChildren = true) {
                    if (resetChildren) {
                        this.districtId = '';
                        this.areaId = '';
                        this.districts = [];
                        this.areas = [];
                    }
                    if (!this.divisionId) return;

                    fetch(`/api/districts?division_id=${this.divisionId}`)
                        .then(res => res.json())
                        .then(data => this.districts = data);
                },

                fetchAreas(resetChildren = true) {
                    if (resetChildren) {
                        this.areaId = '';
                        this.areas = [];
                    }
                    if (!this.districtId) return;

                    fetch(`/api/areas?district_id=${this.districtId}`)
                        .then(res => res.json())
                        .then(data => this.areas = data);
                }
            }));
        });
    </script>
</div>
