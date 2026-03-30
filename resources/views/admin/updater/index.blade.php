@extends('admin.layouts.app')

@section('title', 'System Updater')

@section('content')
<div class="px-4 py-6 md:px-8 max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">System Updater</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">1-click native deployment securely powered by GitHub API.</p>
        </div>
        <div>
            @if($hasConfig)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-lg text-xs font-semibold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                API Connected
            </span>
            @endif
        </div>
    </div>

    @if(!$hasConfig)
    <div class="bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 p-6 mb-8 rounded-r-2xl shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0 text-indigo-500 text-2xl font-bold mt-1">🔌</div>
            <div class="ml-4 w-full">
                <h3 class="text-base font-bold text-indigo-900 dark:text-indigo-300">GitHub API is not initialized!</h3>
                <p class="mt-1 text-sm text-indigo-700 dark:text-indigo-400 mb-4">Before using the updater, you must provide your GitHub repository details. This connects directly to GitHub securely using a Personal Access Token.</p>
                
                <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-inner border border-indigo-100 dark:border-indigo-800 relative z-10 w-full max-w-3xl space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">GitHub Username</label>
                            <input type="text" id="git_user" placeholder="e.g. abdussalamseoex" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Repository Name</label>
                            <input type="text" id="git_repo" placeholder="abdussalamseoex/doctorbd24" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Personal Access Token (PAT)</label>
                        <input type="password" id="git_token" placeholder="ghp_xxxxxxxxxxxxxxxxxxxxx" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 font-mono">
                    </div>
                    <button id="btn-connect" onclick="initializeGit()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm transition-colors shadow-md flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        Connect GitHub Repo
                    </button>
                    <div id="init-error" class="hidden text-sm text-red-500 font-medium"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Update Status & Action --}}
        <div class="lg:col-span-1 border border-gray-200 dark:border-gray-800 rounded-2xl bg-white dark:bg-gray-900 shadow-sm overflow-hidden flex flex-col relative h-full">
            <div class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800 px-6 py-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h3 class="font-bold text-gray-800 dark:text-gray-200">System Status</h3>
            </div>
            
            <div class="p-6 flex-1 flex flex-col justify-center items-center text-center">
                @if(!$hasConfig)
                    <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-700 dark:text-gray-300">Not Connected</h4>
                    <p class="text-sm text-gray-500 mt-2">Link your repository to check for updates.</p>
                @else
                    @if($updateAvailable && $latestCommit)
                        <div class="w-20 h-20 rounded-full bg-amber-50 dark:bg-amber-900/30 border-4 border-amber-100 flex items-center justify-center mb-4 text-amber-500 relative">
                            <span class="absolute top-0 right-0 w-4 h-4 rounded-full bg-red-500 border-2 border-white dark:border-gray-900 animate-ping"></span>
                            <span class="absolute top-0 right-0 w-4 h-4 rounded-full bg-red-500 border-2 border-white dark:border-gray-900"></span>
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200">Update Available!</h4>
                        <div class="mt-3 bg-gray-50 dark:bg-gray-800/80 p-3 rounded-lg border border-gray-100 dark:border-gray-700 text-left w-full text-sm">
                            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Incoming Commit:</div>
                            <p class="font-medium text-gray-900 dark:text-white leading-tight">"{{ Str::limit($latestCommit['commit']['message'], 60) }}"</p>
                            <p class="text-xs text-gray-400 mt-1">by {{ $latestCommit['commit']['author']['name'] }}</p>
                        </div>
                        
                        <button id="btn-update" onclick="startSystemUpdate()" class="mt-6 w-full py-3 px-4 rounded-xl font-bold transition-all shadow-md active:scale-95 text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-90 flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Apply Update Now
                        </button>
                    @else
                        <div class="w-20 h-20 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border-4 border-emerald-100 flex items-center justify-center mb-4 text-emerald-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200">System is Fixed & Updated!</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">You are running the latest code from GitHub.</p>
                        
                        <button id="btn-update" onclick="startSystemUpdate()" class="mt-6 w-full py-2.5 px-4 rounded-xl font-bold transition-all shadow-sm active:scale-95 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Force File Sync
                        </button>
                    @endif
                @endif
            </div>
            
            {{-- Console Overaly Output --}}
            <div id="cli-container" class="hidden absolute inset-0 bg-gray-900 z-10 flex flex-col">
                <div class="bg-black/80 px-4 py-3 border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-xs text-gray-300 font-mono font-bold tracking-widest">EXECUTING...</span>
                    </div>
                </div>
                <div class="p-4 flex-1 overflow-y-auto">
                    <pre id="cli-output" class="text-emerald-400 font-mono text-xs whitespace-pre-wrap leading-relaxed">Initializing request...</pre>
                </div>
                <div class="p-4 bg-black/50 hidden" id="cli-done-actions">
                    <button onclick="window.location.reload()" class="w-full py-2 bg-white text-black font-bold text-sm rounded hover:bg-gray-200 active:scale-95 transition-transform">Reload Dashboard</button>
                </div>
            </div>
        </div>

        {{-- Right Column: History List --}}
        <div class="lg:col-span-2 border border-gray-200 dark:border-gray-800 rounded-2xl bg-white dark:bg-gray-900 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="font-bold text-gray-800 dark:text-gray-200">Update History</h3>
                </div>
                <span class="text-xs text-gray-500 font-medium px-2 py-1 rounded bg-gray-200 dark:bg-gray-700">{{ count($history) }} Updates Total</span>
            </div>
            
            <div class="flex-1 overflow-y-auto max-h-[500px] p-0">
                @if(count($history) > 0)
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($history as $index => $item)
                        <div class="p-5 flex items-start gap-4 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors {{ $index === 0 ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : '' }}">
                            <div class="flex-shrink-0 mt-0.5">
                                @if($index === 0)
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 flex items-center justify-center ring-4 ring-white dark:ring-gray-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="w-px h-full bg-indigo-200 dark:bg-indigo-800 mx-auto mt-2 hidden lg:block"></div>
                                @else
                                    <div class="w-3 h-3 rounded-full bg-gray-300 dark:bg-gray-600 ml-2.5 ring-4 ring-white dark:ring-gray-900"></div>
                                    <div class="w-px h-full bg-gray-200 dark:bg-gray-800 mx-auto mt-2 hidden lg:block"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-2">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate w-3/4">
                                        {{ $item['message'] ?? 'No message' }}
                                    </p>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($item['date'])->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center gap-1 font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-[10px]">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                        {{ substr($item['sha'], 0, 7) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $item['author'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center p-8 text-center">
                        <div class="w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm text-gray-500">No updates have been installed yet.</p>
                        <p class="text-xs text-gray-400 mt-1">Once you apply an update, it will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
async function startSystemUpdate() {
    let btn = document.getElementById('btn-update');
    let cliContainer = document.getElementById('cli-container');
    let out = document.getElementById('cli-output');
    let doneActions = document.getElementById('cli-done-actions');
    
    if(!confirm('Are you ready to pull the latest changes from GitHub via Native PHP? Backups are recommended!')) {
        return;
    }

    cliContainer.classList.remove('hidden');
    out.innerText = "Initializing native API request to GitHub. Do not close this page...\n";

    try {
        let meta = document.querySelector('meta[name="csrf-token"]');
        let res = await fetch('{{ route("admin.updater.run") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': meta ? meta.getAttribute('content') : ''
            }
        });
        
        let data = await res.json();
        
        if(data.success) {
            out.innerText += "\n" + data.output;
            out.classList.replace('text-emerald-400', 'text-green-400');
        } else {
            out.innerText += "\n[FAILED]: " + data.output;
            out.classList.replace('text-emerald-400', 'text-red-400');
        }
        
    } catch (err) {
        out.innerText += "\n[NETWORK ERROR]: " + err.message;
        out.classList.replace('text-emerald-400', 'text-amber-400');
    }
    
    doneActions.classList.remove('hidden');
}

async function initializeGit() {
    let btn = document.getElementById('btn-connect');
    let errBox = document.getElementById('init-error');
    let user = document.getElementById('git_user').value;
    let token = document.getElementById('git_token').value;
    let repo = document.getElementById('git_repo').value;

    errBox.classList.add('hidden');

    if (!user || !token || !repo) {
        errBox.innerText = "Please fill in all GitHub connection fields.";
        errBox.classList.remove('hidden');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Connecting...`;

    try {
        let meta = document.querySelector('meta[name="csrf-token"]');
        let res = await fetch('{{ route("admin.updater.run") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': meta ? meta.getAttribute('content') : ''
            },
            body: JSON.stringify({
                init_git: true,
                git_username: user,
                git_token: token,
                git_repo: repo
            })
        });

        let data = await res.json();

        if(data.success) {
            window.location.reload();
        } else {
            errBox.innerText = "ERROR: " + data.output;
            errBox.classList.remove('hidden');
            btn.innerHTML = `Connect GitHub Repo`;
            btn.disabled = false;
        }
    } catch (err) {
        errBox.innerText = "Network Error: " + err.message;
        errBox.classList.remove('hidden');
        btn.innerHTML = `Connect GitHub Repo`;
        btn.disabled = false;
    }
}
</script>
@endsection
