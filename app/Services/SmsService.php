<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    public static function send($phone, $message)
    {
        // مثال باستخدام خدمة SMS مثل Twilio أو أي خدمة محلية
        // يمكنك تعديل هذا الكود حسب مزود الخدمة الذي تستخدمه
        
        $apiKey = env('SMS_API_KEY');
        $senderId = env('SMS_SENDER_ID');
        
        if (!$apiKey || !$senderId) {
            \Log::warning('SMS credentials not configured');
            return false;
        }
        
        try {
            // مثال لخدمة SMS عربية (يمكنك تعديله)
            $response = Http::post('https://api.sms-service.com/send', [
                'api_key' => $apiKey,
                'sender_id' => $senderId,
                'phone' => $phone,
                'message' => $message,
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }
}