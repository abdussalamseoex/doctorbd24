@extends('admin.layouts.app')
@section('title', 'Settings')
@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm font-medium flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="max-w-4xl mx-auto space-y-6">
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ════ CARD: GENERAL & BRANDING ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center text-sky-600">🏛️</span>
                General Branding
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-1.5">Site Name</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 transition-all">
                </div>

                {{-- Logo Upload --}}
                <div x-data="{ preview: '{{ $settings['site_logo'] ? asset('storage/'.$settings['site_logo']) : '' }}' }">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-3">Site Logo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-700">
                            <template x-if="preview">
                                <img :src="preview" class="w-full h-full object-contain">
                            </template>
                            <template x-if="!preview">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </template>
                        </div>
                        <input type="file" name="site_logo" @change="preview = URL.createObjectURL($event.target.files[0])" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                    </div>
                </div>

                {{-- Favicon Upload --}}
                <div x-data="{ preview: '{{ $settings['favicon'] ? asset('storage/'.$settings['favicon']) : '' }}' }">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-3">Favicon (32x32)</label>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-700">
                            <template x-if="preview">
                                <img :src="preview" class="w-8 h-8 object-contain">
                            </template>
                            <template x-if="!preview">
                                <span class="text-[10px] text-gray-400">ICO</span>
                            </template>
                        </div>
                        <input type="file" name="favicon" @change="preview = URL.createObjectURL($event.target.files[0])" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                    </div>
                </div>
            </div>
        </div>

        {{-- ════ CARD: CONTACT INFORMATION ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">📞</span>
                Contact & Footer
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-1.5">Public Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-all">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-1.5">Public Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}" 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-1.5">Office Address</label>
                    <input type="text" name="contact_address" value="{{ old('contact_address', $settings['contact_address']) }}" 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-1.5">Footer Text (Copyright)</label>
                    <textarea name="footer_text" rows="2" 
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-all resize-none">{{ old('footer_text', $settings['footer_text']) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ════ CARD: SOCIAL MEDIA ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">🌐</span>
                Social Links
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach(['facebook_url', 'twitter_url', 'instagram_url', 'youtube_url'] as $social)
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-1.5">{{ str_replace('_', ' ', ucfirst($social)) }}</label>
                        <input type="url" name="{{ $social }}" value="{{ old($social, $settings[$social]) }}" placeholder="https://..."
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-all">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ════ CARD: SYSTEM SETTINGS ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">⚙️</span>
                System Behavior
            </h3>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Auto-Approve Reviews</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        চালু থাকলে রিভিউ সাবমিটের সাথে সাথেই পাবলিক হবে। বন্ধ থাকলে অ্যাডমিনকে ম্যানুয়ালি Approve করতে হবে।
                    </p>
                </div>
                <div x-data="{ on: {{ $settings['review_auto_approve'] === '1' ? 'true' : 'false' }} }" class="flex-shrink-0">
                    <input type="hidden" name="review_auto_approve" :value="on ? '1' : '0'">
                    <button type="button" @click="on = !on"
                        :class="on ? 'bg-sky-500' : 'bg-gray-300 dark:bg-gray-600'"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <span :class="on ? 'translate-x-6' : 'translate-x-1'"
                               class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"></span>
                    </button>
                </div>
            </div>
        </div>

            {{-- ═══════════════════════════════════════
                 HOMEPAGE CONTENT
            ═══════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Homepage Content</h3>
                        <p class="text-sm text-gray-500">Control hero section and main landing page text.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Hero Title</label>
                        <input type="text" name="homepage_hero_title" value="{{ $settings['homepage_hero_title'] }}" 
                               class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Hero Subtitle</label>
                        <textarea name="homepage_hero_subtitle" rows="2"
                                  class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500 transition-all">{{ $settings['homepage_hero_subtitle'] }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 HOMEPAGE SEO
            ═══════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Homepage SEO</h3>
                        <p class="text-sm text-gray-500">Search engine optimization for the landing page.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Meta Title</label>
                        <input type="text" name="homepage_seo_title" value="{{ $settings['homepage_seo_title'] }}" 
                               class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Meta Description</label>
                        <textarea name="homepage_seo_description" rows="3"
                                  class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500 transition-all">{{ $settings['homepage_seo_description'] }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">SEO Keywords</label>
                        <input type="text" name="homepage_seo_keywords" value="{{ $settings['homepage_seo_keywords'] }}" placeholder="doctor, hospital, ambulance, bangladesh"
                               class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500 transition-all">
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 SEO CRAWLERS & SITEMAP
            ═══════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Robots.txt & Sitemap</h3>
                            <p class="text-sm text-gray-500">Control search engine crawlers and view dynamic sitemap.</p>
                        </div>
                    </div>
                    <div>
                        <a href="{{ url('/sitemap.xml') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-sm font-bold hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            View Live Sitemap
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Custom robots.txt Content</label>
                        <p class="text-xs text-gray-500 mb-3">Leave blank to use default. Be careful, incorrect rules can de-index your website.</p>
                        <textarea name="robots_txt" rows="6" placeholder="User-agent: *&#10;Allow: /" 
                                  class="w-full font-mono rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-green-500 focus:border-green-500 transition-all p-4">{{ $settings['robots_txt'] ?? "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /login\nDisallow: /register\n\nSitemap: " . url('/sitemap.xml') . "\n" }}</textarea>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                        <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Sitemap Status</h4>
                        <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400 font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Dynamic Sitemap is active. Frontend URLs are automatically indexed.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 GOOGLE SERVICES & TRACKING
            ═══════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Google Services & Tracking</h3>
                        <p class="text-sm text-gray-500">Add Google Analytics and Search Console Verification codes here.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Google Analytics Code</label>
                        <p class="text-xs text-gray-500 mb-2">Paste the entire <code>&lt;script&gt;</code> block from Google Analytics.</p>
                        <textarea name="google_analytics" rows="5" placeholder="<!-- Google tag (gtag.js) -->..." 
                                  class="w-full font-mono rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-red-500 focus:border-red-500 transition-all p-4">{{ old('google_analytics', $settings['google_analytics'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Google Search Console Verification</label>
                        <p class="text-xs text-gray-500 mb-2">Paste the meta tag here (e.g. <code>&lt;meta name="google-site-verification" content="..." /&gt;</code>).</p>
                        <input type="text" name="google_search_console" value="{{ old('google_search_console', $settings['google_search_console'] ?? '') }}" placeholder="<meta name='google-site-verification' content='...' />"
                               class="w-full font-mono rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-red-500 focus:border-red-500 transition-all">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-8">
            <button type="submit" class="px-8 py-3 rounded-2xl bg-gradient-to-r from-sky-500 to-blue-600 text-white font-bold shadow-lg shadow-sky-200 dark:shadow-none hover:opacity-90 transition-all">
                Save All Settings
            </button>
        </div>
    </form>
</div>
@endsection
