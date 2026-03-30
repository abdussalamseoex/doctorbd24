@extends('admin.layouts.app')

@section('title', 'System Updater')

@section('content')
<div class="px-4 py-6 md:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">System Updater</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">1-click automated deployment from GitHub Repository.</p>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 rounded-lg text-xs font-semibold uppercase tracking-wider border border-sky-200 dark:border-sky-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Fast Sync
            </span>
        </div>
    </div>

    @if(!$hasGit)
    <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-4 mb-6 rounded-r-lg shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0 text-amber-500 text-xl font-bold mt-0.5">⚠️</div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-amber-800 dark:text-amber-400">Git is not initialized on this server!</h3>
                <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                    <p class="mb-2">Before you can use 1-click update, you must link your GitHub repository. You can do this right here without opening cPanel terminal:</p>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-inner border border-amber-200 dark:border-amber-800 relative z-10 w-full max-w-2xl mt-3 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">GitHub Username</label>
                            <input type="text" id="git_user" placeholder="e.g. john-doe" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Personal Access Token (PAT)</label>
                            <input type="password" id="git_token" placeholder="ghp_xxx..." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Repository URL (without https://)</label>
                            <input type="text" id="git_repo" placeholder="github.com/john-doe/my-project.git" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-amber-500">
                        </div>
                        <button onclick="initializeGit()" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold rounded-lg text-sm transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            Connect GitHub Repo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Version Info</h3>
                
                @if($hasGit)
                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Current Branch</div>
                        <div class="font-bold text-gray-800 dark:text-gray-100 font-mono mt-0.5 bg-gray-50 dark:bg-gray-900 px-2 py-1 rounded inline-block text-sm border border-gray-200 dark:border-gray-700">{{ $branch }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Latest Commit</div>
                        <div class="font-bold text-gray-800 dark:text-gray-100 text-sm mt-0.5">{{ $currentCommit }}</div>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-100 dark:border-gray-700 pt-6">
                    <button id="btn-update" {{ !$hasGit ? 'disabled' : '' }} onclick="startSystemUpdate()" class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl font-bold transition-all shadow-md active:scale-95 text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Check & Apply Update
                    </button>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 text-center mt-3">This action will pull latest code from GitHub and clear caches.</p>
                </div>
                @else
                <div class="text-sm text-gray-500 text-center">
                    Git is not connected. Update disabled.
                </div>
                @endif
            </div>
        </div>
        
        <div class="md:col-span-2">
            <div class="bg-gray-900 rounded-2xl shadow-sm border border-gray-800 h-full flex flex-col overflow-hidden">
                <div class="bg-black/50 px-4 py-2 border-b border-gray-800 flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <div class="text-xs text-gray-500 font-mono ml-2">Terminal Output</div>
                </div>
                <div class="p-4 flex-1">
                    <pre id="cli-output" class="text-green-400 font-mono text-xs whitespace-pre-wrap leading-relaxed">Waiting for update command...</pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function startSystemUpdate() {
    let btn = document.getElementById('btn-update');
    let out = document.getElementById('cli-output');
    
    if(!confirm('Are you sure you want to run the system update? It will pull code from Github and migrate the database. Make sure you have backups!')) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v2m0 16v2m8-10h2M4 12H2m15.364-7.364l1.414-1.414M4.222 19.778l1.414-1.414m12.728 12.728l1.414 1.414M4.222 4.222l1.414 1.414"></path></svg> Updating System...`;
    out.innerText = "Initializing connection...\n";

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
            out.innerText += data.output;
            btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Updated Successfully!`;
            btn.classList.replace('from-emerald-500', 'from-green-600');
            btn.classList.replace('to-teal-600', 'to-green-700');
        } else {
            out.innerText += "\nERROR: " + data.output;
            btn.innerHTML = `Update Failed`;
            btn.disabled = false;
        }

    } catch (err) {
        out.innerText += "\nNetwork Error: " + err.message;
        btn.innerHTML = `Update Failed`;
        btn.disabled = false;
    }
}

async function initializeGit() {
    let out = document.getElementById('cli-output');
    let user = document.getElementById('git_user').value;
    let token = document.getElementById('git_token').value;
    let repo = document.getElementById('git_repo').value;

    if (!user || !token || !repo) {
        alert("Please fill in all GitHub connection fields.");
        return;
    }

    if(!confirm("This will initialize a Git repository on your server and connect it to your GitHub repo. Continue?")) return;

    out.innerText = "Connecting to GitHub...\n";
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
        out.innerText += data.output;

        if(data.success) {
            alert("GitHub connected successfully! The page will now reload.");
            window.location.reload();
        } else {
            out.innerText += "\nERROR: Failed to connect.";
        }
    } catch (err) {
        out.innerText += "\nNetwork Error: " + err.message;
    }
}
</script>
@endsection
