<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // To safely migrate data without losing existing varchars, 
        // we first rename the old columns
        Schema::table('chambers', function (Blueprint $table) {
            $table->renameColumn('address', 'address_old');
            $table->renameColumn('visiting_hours', 'visiting_hours_old');
            $table->renameColumn('closed_days', 'closed_days_old');
        });

        // Add the new JSON columns
        Schema::table('chambers', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->text('visiting_hours')->nullable();
            $table->text('closed_days')->nullable();
        });

        // Manually migrate data via DB facade
        $chambers = \Illuminate\Support\Facades\DB::table('chambers')->get();
        foreach ($chambers as $chamber) {
            \Illuminate\Support\Facades\DB::table('chambers')
                ->where('id', $chamber->id)
                ->update([
                    'address' => $chamber->address_old ? json_encode(['en' => $chamber->address_old], JSON_UNESCAPED_UNICODE) : null,
                    'visiting_hours' => $chamber->visiting_hours_old ? json_encode(['en' => $chamber->visiting_hours_old], JSON_UNESCAPED_UNICODE) : null,
                    'closed_days' => $chamber->closed_days_old ? json_encode(['en' => $chamber->closed_days_old], JSON_UNESCAPED_UNICODE) : null,
                ]);
        }

        // Drop the old columns
        Schema::table('chambers', function (Blueprint $table) {
            $table->dropColumn(['address_old', 'visiting_hours_old', 'closed_days_old']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chambers', function (Blueprint $table) {
            // Re-adding string columns and copying JSON's 'en' value would be complex here,
            // so we just cast back to string and drop json.
            $table->dropColumn(['address', 'visiting_hours', 'closed_days']);
        });
        
        Schema::table('chambers', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('visiting_hours')->nullable();
            $table->string('closed_days')->nullable();
        });
    }
};
