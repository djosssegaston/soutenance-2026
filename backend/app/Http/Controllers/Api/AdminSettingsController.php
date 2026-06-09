<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $all = Setting::pluck('value', 'key');

        return response()->json([
            'success' => true,
            'data' => [
                'platform_name' => $all['platform_name'] ?? config('app.name', 'ALOGOTO'),
                'contact_email' => $all['contact_email'] ?? 'contact@alogoto.bj',
                'currency' => $all['currency'] ?? 'XOF',
                'language' => $all['language'] ?? 'fr',
                'min_amount' => $all['min_amount'] ?? '100000',
                'max_amount' => $all['max_amount'] ?? '50000000',
                'interest_rate' => $all['interest_rate'] ?? '5.5',
                'max_duration' => $all['max_duration'] ?? '60',
                'maintenance_mode' => $all['maintenance_mode'] ?? '0',
                'maintenance_msg' => $all['maintenance_msg'] ?? '',
                'notif_email' => $all['notif_email'] ?? '1',
                'notif_sms' => $all['notif_sms'] ?? '1',
                'notif_push' => $all['notif_push'] ?? '1',
                'notif_audit' => $all['notif_audit'] ?? '1',
                'commission' => $all['commission'] ?? '2.5',
                'min_financement' => $all['min_financement'] ?? '100000',
                'max_financement' => $all['max_financement'] ?? '50000000',
                'registration_open' => $all['registration_open'] ?? '1',
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'platform_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'currency' => 'nullable|string|max:10',
            'language' => 'nullable|string|max:10',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'max_duration' => 'nullable|integer|min:1|max:360',
            'maintenance_mode' => 'nullable|boolean',
            'maintenance_msg' => 'nullable|string',
            'notif_email' => 'nullable|boolean',
            'notif_sms' => 'nullable|boolean',
            'notif_push' => 'nullable|boolean',
            'notif_audit' => 'nullable|boolean',
            'commission' => 'nullable|numeric|min:0|max:100',
            'min_financement' => 'nullable|numeric|min:0',
            'max_financement' => 'nullable|numeric|min:0',
            'registration_open' => 'nullable|boolean',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
            );
        }

        AuditLog::log($request->user()->id, 'Paramètres de la plateforme mis à jour', 'bi-gear', 'primary', 'info');

        return response()->json([
            'success' => true,
            'message' => 'Paramètres mis à jour.',
        ]);
    }
}
