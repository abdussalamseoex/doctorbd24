<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $services = DB::table('hospital_services')->get();
        foreach ($services as $service) {
            $name = $service->service_name;
            $desc = $service->description;

            if (!empty($name) && !str_starts_with(trim($name), '{')) {
                $nameJson = json_encode(['en' => $name, 'bn' => '']);
                DB::table('hospital_services')->where('id', $service->id)->update(['service_name' => $nameJson]);
            }
            
            if (!empty($desc) && !str_starts_with(trim($desc), '{')) {
                $descJson = json_encode(['en' => $desc, 'bn' => '']);
                DB::table('hospital_services')->where('id', $service->id)->update(['description' => $descJson]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
