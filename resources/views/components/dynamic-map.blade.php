@props(['locations' => []])

<div x-data="leafletMapComponent()"
     x-init="initMap()"
     x-effect="reactToLocations($wire.mapLocations)"
     class="relative w-full h-full bg-slate-100 dark:bg-slate-800 rounded-[2rem] overflow-hidden"
>
    <!-- Map Container -->
    <div x-ref="mapContainer" class="w-full h-full z-0 font-sans" style="min-height: 400px;"></div>

    <!-- Loading Overlay -->
    <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-gray-900/70 backdrop-blur-sm shadow-inner flex flex-col items-center justify-center z-10 transition-opacity">
        <svg class="w-10 h-10 text-emerald-500 animate-spin mb-3 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        <span class="text-sm font-bold text-gray-600 dark:text-gray-300 tracking-wider uppercase">{{ __('Loading Map...') }}</span>
    </div>
</div>

@once
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('leafletMapComponent', () => ({
            map: null,
            markers: [],
            layerGroup: null,
            loading: true,
            locs: [],

            initMap() {
                // Ensure leaflet loads
                if (typeof L === 'undefined') {
                    setTimeout(() => this.initMap(), 100);
                    return;
                }
                this.setupMap();
            },

            reactToLocations(newLocations) {
                this.locs = newLocations || [];
                if (this.map) {
                    this.updateMarkers();
                }
            },

            setupMap() {
                this.loading = false;
                
                // Initialize Leaflet Map centered to Bangladesh
                this.map = L.map(this.$refs.mapContainer).setView([23.6850, 90.3563], 7);
                
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                    subdomains: 'abcd',
                    maxZoom: 19
                }).addTo(this.map);

                this.layerGroup = L.featureGroup().addTo(this.map);

                // Delay execution to ensure map sizing
                setTimeout(() => {
                    this.map.invalidateSize();
                    this.updateMarkers();
                }, 300);
            },

            updateMarkers() {
                if (!this.map || !this.layerGroup) return;
                
                // Clear existing markers
                this.layerGroup.clearLayers();
                this.markers = [];

                if (this.locs.length === 0) return;

                let validPins = false;

                const updateBounds = () => {
                    if (validPins) {
                        this.map.fitBounds(this.layerGroup.getBounds(), { padding: [40, 40] });
                        if (this.markers.length === 1 || this.map.getZoom() > 14) {
                            this.map.setZoom(14);
                        }
                    }
                };

                const createMarker = (lat, lng, loc) => {
                    validPins = true;
                    
                    const colorClass = loc.featured ? 'bg-yellow-500' : 'bg-emerald-500';
                    const iconHtml = `<div class="w-5 h-5 rounded-full ${colorClass} border-2 border-white shadow-md transition-transform hover:scale-110"></div>`;
                    
                    const customIcon = L.divIcon({
                        className: 'custom-leaflet-marker',
                        html: iconHtml,
                        iconSize: [20, 20],
                        iconAnchor: [10, 10],
                        popupAnchor: [0, -10]
                    });

                    const marker = L.marker([lat, lng], { icon: customIcon });

                    const content = `
                        <div class="px-2 py-1 min-w-[200px] font-sans">
                            <h3 class="font-extrabold text-[#111827] text-[14px] leading-tight mb-1">${loc.name}</h3>
                            ${loc.type ? `<span class="inline-block px-1.5 py-0.5 mt-1 rounded bg-[#ecfdf5] text-[#059669] text-[9px] uppercase font-bold tracking-wider mb-2 border border-[#a7f3d0]">${loc.type}</span>` : ''}
                            <div class="text-xs text-[#4b5563] mb-3 leading-snug">${loc.address || ''}</div>
                            <a href="${loc.url}" class="block text-center w-full bg-[#10b981] hover:bg-[#059669] text-white text-sm font-bold py-1.5 rounded transition-colors no-underline">View Profile &rarr;</a>
                        </div>
                    `;

                    marker.bindPopup(content);
                    marker.addTo(this.layerGroup);
                    this.markers.push(marker);
                    
                    updateBounds();
                };

                this.locs.forEach((loc, index) => {
                    if (loc.lat && loc.lng) {
                        createMarker(parseFloat(loc.lat), parseFloat(loc.lng), loc);
                    } else if (loc.name || loc.address) {
                        // Nominatim API Geocoding fallback
                        setTimeout(() => {
                            const query = loc.address ? `${loc.name}, ${loc.address}, Bangladesh` : `${loc.name}, Bangladesh`;
                            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data && data.length > 0) {
                                        createMarker(parseFloat(data[0].lat), parseFloat(data[0].lon), loc);
                                    }
                                });
                        }, index * 1100); // Respect nominatim strictly 1 req/sec rating
                    }
                });
            }
        }));
    });
</script>
<style>
    .custom-leaflet-marker { background: transparent; border: none; }
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); padding: 0; }
    .leaflet-popup-content { margin: 8px 10px; }
    .leaflet-container a.leaflet-popup-close-button { color: #9ca3af; right: 4px; top: 4px; }
    /* Hide Leaflet watermark to keep UI extremely clean */
    .leaflet-control-attribution { display: none !important; }
</style>
@endonce
