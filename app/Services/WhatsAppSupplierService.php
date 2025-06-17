<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhatsAppSupplierService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('WHATSAPP_API_KEY', 'LqbEgMK6bpw9aKyZjp9q');
        $this->baseUrl = 'https://api.fonnte.com/send';
    }

    public function sendSupplierOrderRequest($supplierPhone, $supplierName, $barangData)
    {
        if (empty($supplierPhone) || empty($barangData)) {
            return false;
        }

        // Check daily limit per supplier (maksimal 5 pesan per supplier per hari)
        if (!$this->canSendToSupplier($supplierPhone)) {
            Log::info('Daily supplier notification limit reached', ['supplier_phone' => $supplierPhone]);
            return false;
        }

        $message = $this->buildSupplierOrderMessage($supplierName, $barangData);
        $sent = $this->sendMessage($supplierPhone, $message);

        if ($sent) {
            $this->incrementSupplierNotificationCount($supplierPhone);
        }

        return $sent;
    }

    private function buildSupplierOrderMessage($supplierName, $barangData)
    {
        $message = "🛒 *PERMINTAAN PENAWARAN BARANG* 🛒\n\n";
        $message .= "Halo *{$supplierName}*,\n\n";
        $message .= "Kami membutuhkan penawaran untuk barang berikut:\n\n";

        $message .= "📦 *{$barangData['nama_barang']}*\n";
        $message .= "   • Merek: {$barangData['merek']}\n";
        $message .= "   • Stok Saat Ini: {$barangData['stok_saat_ini']} {$barangData['satuan']}\n";
        $message .= "   • Reorder Point: {$barangData['reorder_point']} {$barangData['satuan']}\n\n";

        $message .= "Mohon dapat memberikan:\n";
        $message .= "• Harga per unit\n";
        $message .= "• Minimum order quantity\n";
        $message .= "• Estimasi waktu pengiriman\n";
        $message .= "• Ketersediaan stok\n\n";

        $message .= "⏰ Waktu: " . now()->format('d/m/Y H:i') . "\n";
        $message .= "🏪 Sistem Inventory Management\n\n";
        $message .= "Terima kasih atas kerjasamanya! 🙏";

        return $message;
    }

    private function sendMessage($phone, $message)
    {
        try {
            // Format nomor telepon (hapus 0 di depan dan tambah 62)
            $formattedPhone = $this->formatPhoneNumber($phone);

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post($this->baseUrl, [
                'target' => $formattedPhone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp supplier notification sent successfully', [
                    'phone' => $formattedPhone,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp supplier notification', [
                    'phone' => $formattedPhone,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp supplier notification error', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function formatPhoneNumber($phone)
    {
        // Hapus karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika belum dimulai dengan 62, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    public function canSendToSupplier($supplierPhone)
    {
        $dailyCount = $this->getSupplierDailyNotificationCount($supplierPhone);
        return $dailyCount < 5; // Maksimal 5 pesan per supplier per hari
    }

    public function getSupplierDailyNotificationCount($supplierPhone)
    {
        $cacheKey = "supplier_notification_count_{$supplierPhone}_" . now()->format('Y_m_d');
        return Cache::get($cacheKey, 0);
    }

    public function incrementSupplierNotificationCount($supplierPhone)
    {
        $cacheKey = "supplier_notification_count_{$supplierPhone}_" . now()->format('Y_m_d');
        $currentCount = $this->getSupplierDailyNotificationCount($supplierPhone);
        Cache::put($cacheKey, $currentCount + 1, now()->endOfDay());
    }

    public function getRemainingSupplierNotifications($supplierPhone)
    {
        return 5 - $this->getSupplierDailyNotificationCount($supplierPhone);
    }
}
