<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhatsAppNotificationService
{
    private $apiKey;
    private $baseUrl;
    private $adminPhone;

    public function __construct()
    {
        $this->apiKey = env('WHATSAPP_API_KEY', 'LqbEgMK6bpw9aKyZjp9q');
        $this->baseUrl = 'https://api.fonnte.com/send';
        $this->adminPhone = env('ADMIN_PHONE', '085171155301');
    }

    public function sendReorderNotification($items)
    {
        if (empty($items)) {
            return false;
        }

        // Check daily limit (maksimal 10 notifikasi per hari)
        if (!$this->canSendDailyNotification()) {
            Log::info('Daily notification limit reached (10 notifications per day)');
            return false;
        }

        $message = $this->buildReorderMessage($items);
        $sent = $this->sendMessage($this->adminPhone, $message);

        if ($sent) {
            $this->incrementDailyNotificationCount();
        }

        return $sent;
    }

    private function buildReorderMessage($items)
    {
        $message = "🚨 *PERINGATAN STOK RENDAH* 🚨\n\n";
        $message .= "Barang berikut telah mencapai reorder point:\n\n";

        foreach ($items as $item) {
            $message .= "📦 *{$item['nama_barang']}*\n";
            $message .= "   • Merek: {$item['merek']}\n";
            $message .= "   • Stok: {$item['stok_saat_ini']} {$item['satuan']}\n";
            $message .= "   • Reorder Point: {$item['reorder_point']}\n\n";
        }

        $message .= "⏰ Waktu: " . now()->format('d/m/Y H:i') . "\n";
        $message .= "🏪 Sistem Inventory Management";

        return $message;
    }

    private function sendMessage($phone, $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post($this->baseUrl, [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp notification sent successfully', [
                    'phone' => $phone,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp notification', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp notification error', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function canSendNotification($itemId)
    {
        $cacheKey = "reorder_notification_{$itemId}";
        return !Cache::has($cacheKey);
    }

    public function markNotificationSent($itemId)
    {
        $cacheKey = "reorder_notification_{$itemId}";
        Cache::put($cacheKey, true, now()->addDay());
    }

    public function canSendDailyNotification()
    {
        $dailyCount = $this->getDailyNotificationCount();
        return $dailyCount < 10; // Maksimal 10 notifikasi per hari
    }

    public function getDailyNotificationCount()
    {
        $cacheKey = "daily_notification_count_" . now()->format('Y_m_d');
        return Cache::get($cacheKey, 0);
    }

    public function incrementDailyNotificationCount()
    {
        $cacheKey = "daily_notification_count_" . now()->format('Y_m_d');
        $currentCount = $this->getDailyNotificationCount();
        Cache::put($cacheKey, $currentCount + 1, now()->endOfDay());
    }

    public function getRemainingDailyNotifications()
    {
        return 10 - $this->getDailyNotificationCount();
    }
}
