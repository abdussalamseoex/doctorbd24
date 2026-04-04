@props(['locations' => []])

<div x-data="{ locations: @entangle('mapLocations'), ...dynamicMapComponent() }"
     x-init="initMap()"
     class="relative w-full h-full bg-slate-100 dark:bg-slate-800 rounded-[2rem] overflow-hidden"
>
    <!-- Map Container -->
    <div x-ref="mapContainer" class="w-full h-full z-0 font-sans"></div>

    <!-- Loading Overlay -->
    <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-gray-900/70 backdrop-blur-sm shadow-inner flex flex-col items-center justify-center z-10 transition-opacity">
        <svg class="w-10 h-10 text-emerald-500 animate-spin mb-3 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        <span class="text-sm font-bold text-gray-600 dark:text-gray-300 tracking-wider uppercase">{{ __('Loading Map...') }}</span>
    </div>
</div>

@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dynamicMapComponent', () => ({
            apiKey: '{{ \App\Models\Setting::get("google_maps_api_key", env("GOOGLE_MAPS_API_KEY")) }}',
            map: null,
            markers: [],
            loading: true,

            initMap() {
                if (!window.google || !window.google.maps) {
                    this.loadScript().then(() => this.setupMap()).catch(e => {
                        console.error('Google Maps Script Error:', e);
                        this.loading = false;
                    });
                } else {
                    this.setupMap();
                }

                // Livewire v3 reactivity: watch the entangled locations
                this.$watch('locations', () => {
                    this.updateMarkers();
                });
            },

            loadScript() {
                return new Promise((resolve, reject) => {
                    if (document.getElementById('google-maps-js-api')) {
                        let interval = setInterval(() => {
                            if (window.google && window.google.maps) {
                                clearInterval(interval);
                                resolve();
                            }
                        }, 100);
                        return;
                    }
                    window.googleMapsCallback = () => {
                        resolve();
                        delete window.googleMapsCallback;
                    };

                    const script = document.createElement('script');
                    script.id = 'google-maps-js-api';
                    script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}&libraries=places&callback=googleMapsCallback`;
                    script.async = true;
                    script.defer = true;
                    // script.onload = resolve; // Replaced by callback
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            },

            setupMap() {
                this.loading = false;
                
                // Base map style for clean UI
                const mapOptions = {
                    center: { lat: 23.6850, lng: 90.3563 }, // Default center to Bangladesh
                    zoom: 7,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                    styles: [
                        { featureType: "poi.business", stylers: [{ visibility: "off" }] },
                        { featureType: "transit", elementType: "labels.icon", stylers: [{ visibility: "off" }] }
                    ]
                };

                this.map = new google.maps.Map(this.$refs.mapContainer, mapOptions);
                this.updateMarkers();
            },

            updateMarkers() {
                if (!this.map) return;
                
                // Define the pin colors
                const featuredIcon = 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png';
                const defaultIcon = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';

                // Clear existing markers
                this.markers.forEach(marker => marker.setMap(null));
                this.markers = [];

                const mapLocs = this.locations || [];
                if (mapLocs.length === 0) return;

                const bounds = new google.maps.LatLngBounds();
                const infoWindow = new google.maps.InfoWindow();

                mapLocs.forEach(loc => {
                    if (loc.lat && loc.lng) {
                        const position = { lat: parseFloat(loc.lat), lng: parseFloat(loc.lng) };
                        bounds.extend(position);

                        const marker = new google.maps.Marker({
                            position: position,
                            map: this.map,
                            title: loc.name,
                            animation: google.maps.Animation.DROP,
                            icon: loc.featured ? featuredIcon : defaultIcon
                        });

                        marker.addListener('click', () => {
                            const content = `
                                <div class="px-3 py-2 min-w-[220px] font-sans">
                                    <h3 class="font-extrabold text-[#111827] text-[15px] leading-tight mb-1">${loc.name}</h3>
                                    ${loc.type ? `<span class="inline-block px-2 py-0.5 mt-1 rounded bg-[#ecfdf5] text-[#059669] text-[10px] uppercase font-bold tracking-wider mb-2 border border-[#a7f3d0]">${loc.type}</span>` : ''}
                                    <p class="text-xs text-[#4b5563] mb-3">${loc.address || ''}</p>
                                    <a href="${loc.url}" target="_blank" class="block text-center w-full bg-[#10b981] hover:bg-[#059669] text-white text-xs font-bold py-2 rounded-lg transition-colors">View Profile &rarr;</a>
                                </div>
                            `;
                            infoWindow.setContent(content);
                            infoWindow.open(this.map, marker);
                        });

                        this.markers.push(marker);
                    }
                });

                if (this.markers.length > 0) {
                    this.map.fitBounds(bounds);
                    
                    // Don't zoom in too much if there's only one marker or points are too close
                    if (this.map.getZoom() > 15) {
                        this.map.setZoom(15);
                    }
                    if (this.markers.length === 1) {
                        this.map.setZoom(14);
                    }
                }
            }
        }));
    });
</script>
@endonce
