<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Dashboard') | {{ setting('site_name', 'DoctorBD24') }}</title>
    
    <script>
        // Vanilla JS Dark Mode to prevent FOUC and avoid Alpine <html> binding bugs
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">

<div class="flex h-screen overflow-hidden relative" x-data="{ sidebarOpen: true }">

    {{-- ═══════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════ --}}
    <aside x-bind:class="sidebarOpen ? 'w-64' : 'w-16'"
           class="flex-shrink-0 bg-gray-900 dark:bg-gray-950 text-white transition-all duration-300 flex flex-col border-r border-gray-800">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-4 border-b border-gray-800 gap-3">
            @if(setting('site_logo'))
                <img src="{{ asset('storage/' . setting('site_logo')) }}" alt="{{ setting('site_name', 'DoctorBD24') }}" class="h-8 w-auto">
            @else
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
            @endif
            <span x-show="sidebarOpen" x-transition class="font-bold text-sm whitespace-nowrap bg-gradient-to-r from-sky-400 to-indigo-400 bg-clip-text text-transparent">{{ setting('site_name', 'DoctorBD24') }} Admin</span>
        </div>

        {{-- Nav links --}}
        <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-2">
            @php
            $dashboardRoute = 'admin.dashboard';
            $reviewsRoute = 'admin.reviews.index';
            $reviewsLabel = 'Reviews';
            $reviewsPermission = 'manage reviews';
            
            if (auth()->check() && auth()->user()->hasRole('doctor')) {
                $dashboardRoute = 'doctor.dashboard';
                $reviewsRoute = 'doctor.reviews.index';
                $reviewsLabel = 'My Reviews';
                $reviewsPermission = null;
            } elseif (auth()->check() && auth()->user()->hasRole('hospital')) {
                $dashboardRoute = 'hospital.dashboard';
                $reviewsRoute = 'hospital.reviews.index';
                $reviewsLabel = 'My Reviews';
                $reviewsPermission = null;
            }

            $navItems = [
                ['route' => $dashboardRoute,         'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard', 'permission' => null],
                ['route' => 'admin.doctors.index',     'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Doctors', 'permission' => 'manage doctors'],
                ['route' => 'admin.hospitals.index',   'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'Hospitals', 'permission' => 'manage hospitals'],
                ['route' => 'admin.ambulances.index',  'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'Ambulances', 'permission' => 'manage hospitals'],
                ['route' => 'admin.ambulance-types.index', 'icon' => 'M9 5l7 7-7 7', 'label' => 'Ambulance Types', 'permission' => 'manage settings', 'is_submenu' => true],
                ['route' => 'admin.ambulance-features.index', 'icon' => 'M9 5l7 7-7 7', 'label' => 'Service Features', 'permission' => 'manage settings', 'is_submenu' => true],
                ['route' => 'admin.blog-posts.index',  'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6', 'label' => 'Blog Posts', 'permission' => 'manage blog'],
                ['route' => 'admin.blog-categories.index', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'label' => 'Blog Categories', 'permission' => 'manage settings'],
                ['route' => 'admin.specialties.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'Specialties', 'permission' => 'manage settings'],
                ['route' => 'admin.locations.index',    'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Locations', 'permission' => 'manage settings'],

                ['route' => 'admin.join-requests.index','icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Join Requests', 'permission' => 'manage users'],
                ['route' => 'admin.claim-requests.index','icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Profile Claims', 'permission' => 'manage claims'],
                ['route' => $reviewsRoute,     'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'label' => $reviewsLabel, 'permission' => $reviewsPermission],
                ['route' => 'admin.advertisements.index', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'label' => 'Ads & Banners', 'permission' => 'manage settings'],
                ['route' => 'admin.users.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Users', 'permission' => 'manage users'],
                ['route' => 'admin.roles.index', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Roles & Perms.', 'permission' => 'manage roles'],
                ['route' => 'admin.pages.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Pages Builder', 'permission' => 'manage settings'],
                ['route' => 'admin.contact-messages.index', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Contact Inbox', 'permission' => 'manage users'],
                ['route' => 'admin.activity-logs.index', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Activity Logs', 'permission' => 'manage settings'],
                ['route' => 'admin.redirect-logs.index', 'icon' => 'M13 5l7 7-7 7M5 5l7 7-7 7', 'label' => 'Redirect Logs', 'permission' => 'manage settings'],
                ['route' => 'admin.settings.index',   'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'General Settings', 'permission' => 'manage settings'],
                ['route' => 'admin.ai-settings.index', 'icon' => 'M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5', 'label' => 'AI Agent Settings', 'permission' => 'manage settings'],
                ['route' => 'admin.seo-landing-pages.index', 'icon' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418', 'label' => 'Programmatic SEO', 'permission' => 'manage settings'],
                ['route' => 'admin.updater.index', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'label' => 'System Updater', 'role' => 'admin'],
                
                ['route' => 'doctor.profile.edit', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'My Doctor Profile', 'role' => 'doctor'],
                ['route' => 'hospital.profile.edit', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'My Hospital Profile', 'role' => 'hospital'],
            ];
            @endphp

            @foreach($navItems as $item)
                @php
                    $show = false;
                    if (isset($item['role'])) {
                        $show = auth()->user()->hasRole($item['role']);
                    } else if (isset($item['permission'])) {
                        $show = auth()->user()->hasPermissionTo($item['permission']) || auth()->user()->hasRole('admin');
                    } else {
                        $show = true;
                    }
                @endphp
                @if($show && (!isset($item['route']) || Route::has($item['route'])))
                    <a href="{{ route($item['route']) }}"
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all
                           {{ isset($item['is_submenu']) && $item['is_submenu'] ? 'ml-6 border-l-2 border-transparent hover:border-sky-500 py-2' : '' }}
                           {{ request()->routeIs($item['route']) ? 'bg-sky-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="pointer-events-none w-5 h-5 flex-shrink-0 {{ isset($item['is_submenu']) && $item['is_submenu'] ? 'scale-75 opacity-50' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span x-show="sidebarOpen" x-transition class="pointer-events-none text-sm font-medium whitespace-nowrap {{ isset($item['is_submenu']) && $item['is_submenu'] ? 'text-xs uppercase tracking-wider' : '' }}">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- Bottom --}}
        <div class="border-t border-gray-800 p-3 space-y-2">
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-all text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">View Site</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-gray-400 hover:bg-red-900/30 hover:text-red-400 transition-all text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════
         MAIN AREA
    ═══════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4 md:px-6 flex-shrink-0">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="font-bold text-gray-700 dark:text-gray-200 text-sm md:text-base">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                {{-- AI Assistant Topbar Trigger --}}
                <button @click="$dispatch('open-ai-assistant')" id="topbar-ai-btn"
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-800/40 transition-colors font-medium text-xs shadow-sm" title="AI Assistant">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    AI Assistant
                </button>
                <button @click="$dispatch('open-ai-assistant')"
                        class="w-9 h-9 md:hidden flex items-center justify-center rounded-lg border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-800/40 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </button>

                {{-- Dark mode --}}
                <button onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'))" 
                        class="p-2 text-gray-500 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white rounded-lg transition-colors focus:ring-2 focus:ring-sky-500">
                    <svg class="dark:hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg class="hidden dark:block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                {{-- Admin avatar --}}
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                        {{ mb_substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hidden md:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mx-4 md:mx-6 mt-4 p-3 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm border border-green-200 dark:border-green-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mx-4 md:mx-6 mt-4 p-3 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm border border-red-200 dark:border-red-800 flex items-center gap-2">
            ⚠ {{ session('error') }}
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            @yield('content')
        </main>
    </div>
</div>

<!-- Global Delete Form -->
<form id="global-delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete(url, message = '') {
        const form = document.getElementById('global-delete-form');
        form.action = url;
        form.submit();
    }
</script>

<script>
document.addEventListener('alpine:init', () => {
    // ID-based picker (for admin & join forms)
    Alpine.data('locationPicker', (initialDiv, initialDist, initialArea) => {
        console.log('Alpine.data(locationPicker) registered factory:', {initialDiv, initialDist, initialArea});
        return {
        divisionId: initialDiv ? String(initialDiv) : '',
        districtId: initialDist ? String(initialDist) : '',
        areaId:     initialArea ? String(initialArea) : '',
        districts: [],
        areas: [],
        async init() {
            if (this.divisionId) {
                await this.fetchDistricts(true);
                if (this.districtId) await this.fetchAreas(true);
            }
        },
        async fetchDistricts(isInit = false) {
            this.districts = []; 
            if (!isInit) this.areas = [];
            const keep = this.districtId;
            this.districtId = ''; 
            if (!isInit) this.areaId = '';
            if (!this.divisionId) return;
            try {
                let res = await fetch(`/api/districts?division_id=${this.divisionId}`);
                this.districts = await res.json();
            } catch (err) {
                console.error('fetchDistricts() ERROR:', err);
            }
            if (keep && this.districts.some(d => String(d.id) === keep)) this.districtId = keep;
        },
        async fetchAreas(isInit = false) {
            this.areas = [];
            const keep = this.areaId;
            this.areaId = '';
            if (!this.districtId) return;
            try {
                let res = await fetch(`/api/areas?district_id=${this.districtId}`);
                this.areas = await res.json();
            } catch (err) {
                console.error('fetchAreas() ERROR:', err);
            }
            if (keep && this.areas.some(a => String(a.id) === keep)) this.areaId = keep;
        }
    };
    });
});
</script>
@include('admin.shared._ai_assistant')
<script src="{{ asset('js/admin-ai.js') }}"></script>
@livewireScripts
@stack('scripts')
</body>
</html>
