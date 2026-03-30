<div class="bg-white dark:bg-gray-800 w-full max-w-lg p-6 rounded-2xl">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Bulk Import {{ ucfirst($importType) }}s (CSV)</h3>
        @if(!$isImporting)
        <button type="button" onclick="document.getElementById('importModal').close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        @endif
    </div>

    @if(!$isImporting && $successCount === 0 && $errorCount === 0)
        <!-- Upload Form -->
        <div class="space-y-4">
            <p class="text-sm text-gray-500 font-bangla">CSV টেমপ্লেট ডাউনলোড করুন, আপনার ডাটা পূর্ণ করুন এবং এখানে আপলোড করুন। বড় ফাইল (৬৪ মেগাবাইট পর্যন্ত) সাপোর্ট করবে।</p>
            
            <a href="{{ route('admin.templates.' . Str::slug($importType)) }}" class="flex items-center gap-2 text-sky-600 dark:text-sky-400 text-sm font-medium hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download CSV Template
            </a>

            <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center hover:border-sky-400 dark:hover:border-sky-500 transition-colors group relative">
                <input type="file" wire:model="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                <div class="space-y-2">
                    <svg class="w-10 h-10 mx-auto text-gray-400 group-hover:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-sky-600 font-medium">Click to upload</span> or drag and drop
                    </div>
                    <p class="text-xs text-gray-400">CSV, XLSX up to 64MB</p>
                </div>
            </div>

            @error('file') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

            @if($file)
                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg flex items-center justify-between border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $file->getClientOriginalName() }}</span>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" wire:click="$set('file', null)" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</button>
                    <button wire:click="startImport" class="px-6 py-2 bg-gradient-to-r from-sky-600 to-blue-700 text-white rounded-xl text-sm font-bold shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
                        Start Importing
                    </button>
                </div>
            @endif
        </div>
    @elseif($isImporting)
        <!-- Progress State -->
        <div class="py-10 text-center space-y-6">
            <div class="relative w-32 h-32 mx-auto">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="8" fill="transparent" class="text-gray-100 dark:text-gray-700" />
                    <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="283" stroke-dashoffset="{{ 283 - (283 * $progress / 100) }}" class="text-sky-500 transition-all duration-500" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $progress }}%</span>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Importing Data...</h4>
                <p class="text-sm text-gray-500">Processing row {{ $processedRows }} of {{ $totalRows }}</p>
            </div>
            <div class="flex justify-center gap-8 text-sm pt-4">
                <div class="text-center">
                    <span class="block text-green-500 font-bold text-lg">{{ $successCount }}</span>
                    <span class="text-gray-400">Success</span>
                </div>
                <div class="text-center">
                    <span class="block text-red-500 font-bold text-lg">{{ $errorCount }}</span>
                    <span class="text-gray-400">Errors</span>
                </div>
            </div>
        </div>
    @else
        <!-- Results Summary -->
        <div class="space-y-6">
            <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 dark:text-white">Import Completed!</h4>
                <div class="flex justify-center gap-12 mt-6">
                    <div>
                        <span class="block text-2xl font-black text-green-600">{{ $successCount }}</span>
                        <span class="text-xs text-gray-400 uppercase tracking-widest font-bold">Imported</span>
                    </div>
                    <div>
                        <span class="block text-2xl font-black text-red-500">{{ $errorCount }}</span>
                        <span class="text-xs text-gray-400 uppercase tracking-widest font-bold">Failed</span>
                    </div>
                </div>
            </div>

            @if(count($importErrors) > 0)
                <div class="max-h-48 overflow-y-auto border border-red-100 dark:border-red-900/30 rounded-xl bg-red-50/50 dark:bg-red-900/10 p-4">
                    <h5 class="text-xs font-bold text-red-700 dark:text-red-400 uppercase mb-2">Error Log</h5>
                    <ul class="space-y-1">
                        @foreach($importErrors as $error)
                            <li class="text-xs text-red-600 dark:text-red-400">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="button" @if($importType === 'blog') onclick="window.location.reload()" @else onclick="document.getElementById('importModal').close()" @endif class="w-full py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold hover:opacity-90 transition-all">
                Close & Refresh
            </button>
        </div>
    @endif
</div>
