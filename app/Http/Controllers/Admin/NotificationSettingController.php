<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationSetting;

class NotificationSettingController extends Controller
{
    public function index()
    {
        $setting = auth()->user()->notificationSetting;
        return view('admin.notifications.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_enabled' => 'boolean',
            'whatsapp_phone' => 'nullable|string',
            'telegram_enabled' => 'boolean',
            'telegram_chat_id' => 'nullable|string',
            'daily_report_time' => 'nullable|date_format:H:i',
            'analysis_reminder_time' => 'nullable|date_format:H:i',
            'followup_reminder_time' => 'nullable|date_format:H:i',
            'financial_report_enabled' => 'boolean',
            'doctor_report_enabled' => 'boolean',
            'patient_reminder_enabled' => 'boolean'
        ]);

        $setting = auth()->user()->notificationSetting;

        $setting->update([
            'whatsapp_enabled' => $request->whatsapp_enabled ?? false,
            'whatsapp_phone' => $request->whatsapp_phone,
            'telegram_enabled' => $request->telegram_enabled ?? false,
            'telegram_chat_id' => $request->telegram_chat_id,
            'daily_report_time' => $request->daily_report_time,
            'analysis_reminder_time' => $request->analysis_reminder_time,
            'followup_reminder_time' => $request->followup_reminder_time,
            'financial_report_enabled' => $request->financial_report_enabled ?? false,
            'doctor_report_enabled' => $request->doctor_report_enabled ?? false,
            'patient_reminder_enabled' => $request->patient_reminder_enabled ?? false
        ]);

        return back()->with('success', 'تم تحديث إعدادات الإشعارات بنجاح');
    }
}