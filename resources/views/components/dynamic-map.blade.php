@props(['locations' => []])

<div x-data="dynamicMapComponent()"
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

    <!-- Error Overlay -->
    <div x-show="apiError" style="display: none;" class="absolute inset-0 bg-white dark:bg-gray-800 flex flex-col items-center justify-center z-10 p-6 text-center border border-dashed border-red-300 dark:border-red-800 rounded-[2rem]">
        <svg class="w-12 h-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('Map Unavailable') }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">{{ __('The embedded map experienced an error while loading.') }}</p>
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
            apiError: false,
            locs: [],

            initMap() {
                if (!this.apiKey || this.apiKey === 'YOUR_GOOGLE_MAPS_API_KEY_HERE') {
                    this.loading = false;
                    this.apiError = true;
                    return;
                }

                if (!window.google || !window.google.maps) {
                    this.loadScript().then(() => {
                        this.setupMap();
                    }).catch(e => {
                        console.error('Google Maps Script Error:', e);
                        this.loading = false;
                        this.apiError = true;
                    });
                } else {
                    this.setupMap();
                }
            },

            reactToLocations(newLocations) {
                this.locs = newLocations || [];
                if (this.map) {
                    this.updateMarkers();
                }
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
                    script.onerror = () => reject(new Error('Network error loading map'));
                    document.head.appendChild(script);

                    // Timeout fallback for unresponsive script
                    setTimeout(() => {
                        if (!window.google || !window.google.maps) {
                            reject(new Error('Google Maps script timeout'));
                        }
                    }, 10000);
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
                const featuredIcon = 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png';
                const defaultIcon = 'https://maps.google.com/mapfiles/ms/icons/red-dot.png';

                // Clear existing markers
                this.markers.forEach(marker => marker.setMap(null));
                this.markers = [];

                if (!this.locs || this.locs.length === 0) return;

                const bounds = new google.maps.LatLngBounds();
                const infoWindow = new google.maps.InfoWindow();
                const geocoder = new google.maps.Geocoder();

                let validPins = false;

                const updateBounds = () => {
                    if (validPins) {
                        this.map.fitBounds(bounds);
                        if (this.markers.length === 1 || this.map.getZoom() > 15) {
                            this.map.setZoom(15);
                        }
                    }
                };

                this.locs.forEach((loc, index) => {
                    const createMarker = (position) => {
                        bounds.extend(position);
                        validPins = true;

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
                        updateBounds();
                    };

                    if (loc.lat && loc.lng) {
                        createMarker({ lat: parseFloat(loc.lat), lng: parseFloat(loc.lng) });
                    } else if (loc.name || loc.address) {
                        setTimeout(() => {
                            const query = loc.address ? `${loc.name}, ${loc.address}, Bangladesh` : `${loc.name}, Bangladesh`;
                            geocoder.geocode({ address: query }, (results, status) => {
                                if (status === 'OK' && results[0]) {
                                    createMarker(results[0].geometry.location);
                                } else if (status === 'ZERO_RESULTS' && loc.address) {
                                    geocoder.geocode({ address: `${loc.address}, Bangladesh` }, (res2, stat2) => {
                                        if (stat2 === 'OK' && res2[0]) {
                                            createMarker(res2[0].geometry.location);
                                        } else {
                                            geocoder.geocode({ address: `${loc.name}, Bangladesh` }, (res3, stat3) => {
                                                if (stat3 === 'OK' && res3[0]) createMarker(res3[0].geometry.location);
                                            });
                                        }
                                    });
                                }
                            });
                        }, index * 350); 
                    }
                });
            }
        }));
    });
</script>
@endonce
