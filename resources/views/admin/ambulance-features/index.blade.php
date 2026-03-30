@extends('admin.layouts.app')
@section('title', 'Manage Service Features')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Service Features</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manage the available amenities (Oxygen, First Aid) that ambulances can tag on their profiles.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="featureManager()">
    {{-- Table Column (Left) --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Dictionary Item Name</th>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($features as $f)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-gray-100">{{ $f->name }}</td>
                        <td class="px-6 py-4">
                            @if($f->is_active)
                                <span class="px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium">Active</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-medium">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button @click="editFeature({{ $f->id }}, '{{ addslashes($f->name) }}', {{ $f->is_active ? 'true' : 'false' }})" class="inline-flex p-2 items-center justify-center rounded-lg text-sky-600 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-sky-500/10 transition-colors cursor-pointer" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('admin.ambulance-features.destroy', $f->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this feature dictionary element? Elements bound mathematically will be stripped recursively.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex p-2 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 text-sm">
                            No ambulance features configured.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Column (Right) --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 h-max sticky top-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4" x-text="isEditing ? 'Edit Feature Name' : 'Add New Feature'"></h3>
        
        <form :action="formAction" method="POST" class="space-y-4">
            @csrf
            <template x-if="isEditing">
                <input type="hidden" name="_method" value="PUT">
            </template>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Feature Payload String <span class="text-red-500">*</span></label>
                <input type="text" name="name" x-model="formName" required placeholder="e.g. Paramedic Escort"
                       class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 dark:text-white">
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" x-model="formActive" class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">Active (Visible in configurations)</span>
            </label>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-xl transition-all shadow-sm" x-text="isEditing ? 'Update Configuration' : 'Create Feature'"></button>
                <button type="button" x-show="isEditing" @click="resetForm()" class="px-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('featureManager', () => ({
        isEditing: false,
        formAction: '{{ route('admin.ambulance-features.store') }}',
        formName: '',
        formActive: true,
        
        editFeature(id, name, active) {
            this.isEditing = true;
            this.formAction = `/admin/ambulance-features/${id}`;
            this.formName = name;
            this.formActive = active;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        
        resetForm() {
            this.isEditing = false;
            this.formAction = '{{ route('admin.ambulance-features.store') }}';
            this.formName = '';
            this.formActive = true;
        }
    }));
});
</script>
@endpush
@endsection
