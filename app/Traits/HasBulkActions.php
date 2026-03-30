<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HasBulkActions
{
    /**
     * Handle bulk actions for the model.
     * Requires $this->model to be defined in the controller.
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No items selected.');
        }

        if (!isset($this->model)) {
            Log::error('Bulk action failed: $this->model not defined in ' . get_class($this));
            return back()->with('error', 'Internal error: Model not defined.');
        }

        $tableName = (new $this->model)->getTable();

        switch ($action) {
            case 'delete':
                $count = $this->model::whereIn('id', $ids)->count();
                $this->model::whereIn('id', $ids)->delete();
                if (auth()->user()) {
                    Log::info("User " . auth()->user()->name . " bulk deleted {$count} " . class_basename($this->model));
                }
                return back()->with('success', "{$count} items deleted successfully.");

            case 'publish':
            case 'activate':
                if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'published_at')) {
                    $this->model::whereIn('id', $ids)->update(['published_at' => now()]);
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'active')) {
                    $this->model::whereIn('id', $ids)->update(['active' => true]);
                }
                return back()->with('success', count($ids) . ' items published/activated.');

            case 'draft':
            case 'deactivate':
                if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'published_at')) {
                    $this->model::whereIn('id', $ids)->update(['published_at' => null]);
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'active')) {
                    $this->model::whereIn('id', $ids)->update(['active' => false]);
                }
                return back()->with('success', count($ids) . ' items moved to draft/deactivated.');

            case 'verify':
                $col = \Illuminate\Support\Facades\Schema::hasColumn($tableName, 'verified') ? 'verified' : (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'is_verified') ? 'is_verified' : null);
                if ($col) {
                    $this->model::whereIn('id', $ids)->update([$col => true]);
                    return back()->with('success', count($ids) . ' items verified.');
                }
                return back()->with('error', 'Verification not supported for this model.');

            case 'unverify':
                $col = \Illuminate\Support\Facades\Schema::hasColumn($tableName, 'verified') ? 'verified' : (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'is_verified') ? 'is_verified' : null);
                if ($col) {
                    $this->model::whereIn('id', $ids)->update([$col => false]);
                    return back()->with('success', count($ids) . ' items unverified.');
                }
                return back()->with('error', 'Verification not supported for this model.');

            case 'feature':
                $col = \Illuminate\Support\Facades\Schema::hasColumn($tableName, 'featured') ? 'featured' : (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'is_featured') ? 'is_featured' : null);
                if ($col) {
                    $this->model::whereIn('id', $ids)->update([$col => true]);
                    return back()->with('success', count($ids) . ' items featured.');
                }
                return back()->with('error', 'Featuring not supported for this model.');

            case 'unfeature':
                $col = \Illuminate\Support\Facades\Schema::hasColumn($tableName, 'featured') ? 'featured' : (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'is_featured') ? 'is_featured' : null);
                if ($col) {
                    $this->model::whereIn('id', $ids)->update([$col => false]);
                    return back()->with('success', count($ids) . ' items unfeatured.');
                }
                return back()->with('error', 'Featuring not supported for this model.');

            default:
                return back()->with('error', 'Unsupported bulk action.');
        }
    }
}
