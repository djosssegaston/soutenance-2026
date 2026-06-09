<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('settings');

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $defaults = [
            'platform_name' => 'ALOGOTO',
            'contact_email' => 'contact@alogoto.bj',
            'currency' => 'XOF',
            'language' => 'fr',
            'min_amount' => '100000',
            'max_amount' => '50000000',
            'interest_rate' => '5.5',
            'max_duration' => '60',
            'maintenance_mode' => '0',
            'maintenance_msg' => '',
            'notif_email' => '1',
            'notif_sms' => '1',
            'notif_push' => '1',
            'notif_audit' => '1',
            'commission' => '2.5',
            'min_financement' => '100000',
            'max_financement' => '50000000',
            'registration_open' => '1',
        ];

        foreach ($defaults as $key => $value) {
            Setting::create(compact('key', 'value'));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
