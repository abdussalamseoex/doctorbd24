<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BulkImporter extends Component
{
    use WithFileUploads;

    public $file;
    public $importType; // 'blog', 'doctor', 'hospital', etc.
    public $isImporting = false;
    public $progress = 0;
    public $totalRows = 0;
    public $processedRows = 0;
    public $successCount = 0;
    public $errorCount = 0;
    public $importErrors = [];

    public function mount($type)
    {
        $this->importType = $type;
    }

    public function startImport()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt,xlsx|max:65536', // 64MB
        ]);

        $this->isImporting = true;
        $this->progress = 0;
        $this->processedRows = 0;
        $this->successCount = 0;
        $this->errorCount = 0;
        $this->importErrors = [];

        $path = $this->file->getRealPath();
        
        // Count total rows (excluding header)
        $fileHandle = fopen($path, 'r');
        $this->totalRows = 0;
        while (fgetcsv($fileHandle) !== false) {
            $this->totalRows++;
        }
        $this->totalRows--; // Subtract header
        fclose($fileHandle);

        if ($this->totalRows <= 0) {
            $this->addError('file', 'The file is empty or has no data rows.');
            $this->isImporting = false;
            return;
        }

        // We use a stream/chunk approach for "live" feel in Livewire
        // For now, let's use the Excel::import but if we want truly live updates per row,
        // we should manually process the CSV in this method or a background job.
        // Since Livewire doesn't easily show progress of a synchronous call,
        // we'll process it in chunks here for demonstration.
        
        $this->processCsv($path);
    }

    protected function processCsv($path)
    {
        $fileHandle = fopen($path, 'r');
        $headers = fgetcsv($fileHandle);
        
        // Clean and slugify headers to match Maatwebsite\Excel WithHeadingRow behavior
        foreach ($headers as $i => $header) {
            $cleanHeader = trim(str_replace("\xEF\xBB\xBF", '', $header));
            // Maatwebsite\Excel by default uses snake_case/slug for headers
            $headers[$i] = str_replace('-', '_', Str::slug($cleanHeader));
        }

        $importClass = $this->getImportClass();
        $batchSize = 10;
        $currentRow = 0;

        while (($data = fgetcsv($fileHandle)) !== false) {
            $currentRow++;
            $row = array_combine($headers, $data);
            
            try {
                // We reuse the logic from our Import classes but manually to track progress
                $this->importRow($importClass, $row);
                $this->successCount++;
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->importErrors[] = "Row {$currentRow}: " . $e->getMessage();
            }

            $this->processedRows = $currentRow;
            $this->progress = min(100, round(($this->processedRows / $this->totalRows) * 100));
            
            // Periodically push updates to frontend (this works in Livewire 3/4)
            if ($currentRow % $batchSize === 0) {
                // Optional: sleep(0.1); // Slow down for visual effect
            }
        }
        fclose($fileHandle);
        $this->isImporting = false;
        
        $this->dispatch('importCompleted', [
            'success' => $this->successCount,
            'errors' => $this->errorCount
        ]);
    }

    protected function getImportClass()
    {
        return match($this->importType) {
            'blog' => \App\Imports\BlogPostImport::class,
            'doctor' => \App\Imports\DoctorImport::class,
            'hospital' => \App\Imports\HospitalImport::class,
            'ambulance' => \App\Imports\AmbulanceImport::class,
            'location' => \App\Imports\LocationImport::class,
            default => throw new \Exception("Invalid import type")
        };
    }

    protected function importRow($class, $row)
    {
        $importer = new $class();
        $model = $importer->model($row);
        if ($model) {
            $model->save();
        }
    }

    public function render()
    {
        return view('livewire.admin.bulk-importer');
    }
}
