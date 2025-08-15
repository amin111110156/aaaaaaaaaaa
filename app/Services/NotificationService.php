<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public static function sendWhatsApp($phone, $message)
    {
        // مثال باستخدام خدمة WhatsApp Business API أو أي خدمة مشابهة
        // يمكنك تعديل هذا الكود حسب الخدمة التي تستخدمها
        
        $apiKey = env('WHATSAPP_API_KEY');
        $instanceId = env('WHATSAPP_INSTANCE_ID');
        
        if (!$apiKey || !$instanceId) {
            Log::warning('WhatsApp credentials not configured');
            return false;
        }
        
        try {
            $response = Http::post("https://api.whatsapp-service.com/send", [
                'api_key' => $apiKey,
                'instance_id' => $instanceId,
                'phone' => $phone,
                'message' => $message
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function sendTelegram($chatId, $message)
    {
        $botToken = config('services.telegram.bot_token');
        
        if (!$botToken || !$chatId) {
            Log::warning('Telegram credentials not configured');
            return false;
        }
        
        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function sendNotification($user, $message, $type = 'general')
    {
        $setting = $user->notificationSetting;
        
        if (!$setting) {
            return;
        }
        
        // إرسال عبر WhatsApp
        if ($setting->whatsapp_enabled && $setting->whatsapp_phone) {
            self::sendWhatsApp($setting->whatsapp_phone, $message);
        }
        
        // إرسال عبر Telegram
        if ($setting->telegram_enabled && $setting->telegram_chat_id) {
            self::sendTelegram($setting->telegram_chat_id, $message);
        }
        
        // إرسال عبر البريد الإلكتروني (كما كان)
        if ($type === 'email') {
            $user->notify(new \App\Notifications\AppointmentNotification(null, 'reminder'));
        }
    }
}