<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use App\Models\ReorderPoint;
use App\Services\WhatsAppNotificationService;
use Illuminate\Support\Facades\Log;

class CheckReorderPoint extends Command
{
    protected $signature = 'reorder:check {--debug : Show debug information}';
    protected $description = 'Check reorder point and send WhatsApp notifications automatically';
    protected $whatsappService;

    public function __construct(WhatsAppNotificationService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    public function handle()
    {
        $this->info('🔍 Checking reorder points...');
        Log::info('Automated reorder point check started');

        // Debug mode
        $debug = $this->option('debug');

        if ($debug) {
            $this->info('🐛 Debug mode enabled');
            $dailyStatus = $this->whatsappService->getDailyNotificationCount();
            $this->info("📊 Daily notifications sent: {$dailyStatus}/10");
        }

        // Get barang yang memerlukan perhatian (reorder point)
        $barangs = Barang::orderBy('nama_barang')->get();
        $notifications = [];
        $lowStockItems = 0;

        foreach ($barangs as $barang) {
            // Get latest reorder point data
            $reorderPoint = ReorderPoint::where('barang_id', $barang->id)
                ->latest()
                ->first();

            // Check status dan buat notifikasi - HANYA berdasarkan reorder point
            if ($reorderPoint && $barang->stok <= $reorderPoint->hasil) {
                $lowStockItems++;

                $notifications[] = [
                    'id' => $barang->id,
                    'nama_barang' => $barang->nama_barang,
                    'merek' => $barang->merek,
                    'stok_saat_ini' => $barang->stok,
                    'satuan' => $barang->satuan,
                    'reorder_point' => $reorderPoint->hasil,
                    'status' => 'Reorder Point',
                    'status_class' => 'warning',
                    'priority' => 1,
                    'created_at' => $barang->updated_at
                ];

                if ($debug) {
                    $notifStatus = $this->whatsappService->getNotificationStatus($barang->id);
                    $this->warn("📦 {$barang->nama_barang} - Stok: {$barang->stok} | Reorder: {$reorderPoint->hasil}");
                    $this->line("   Can send: " . ($this->whatsappService->canSendNotification($barang->id, $barang->stok) ? 'YES' : 'NO'));
                    $this->line("   Cache: " . json_encode($notifStatus['item_cache']));
                }
            }
        }

        // Urutkan notifikasi berdasarkan stok terendah
        usort($notifications, function ($a, $b) {
            return $a['stok_saat_ini'] <=> $b['stok_saat_ini'];
        });

        if (!empty($notifications)) {
            $this->warn("⚠️  Found {$lowStockItems} items that reached reorder point:");

            // Tampilkan daftar barang
            foreach ($notifications as $index => $item) {
                $this->line(($index + 1) . ". {$item['nama_barang']} ({$item['merek']}) - Stok: {$item['stok_saat_ini']} {$item['satuan']} | Reorder Point: {$item['reorder_point']}");
            }

            // Kirim notifikasi WhatsApp
            $this->checkAndNotifyReorderPoint($notifications, $debug);
        } else {
            $this->info('✅ All items are above reorder point. No notification needed.');
            Log::info('Automated reorder point check completed - No items need reordering');
        }

        if ($debug) {
            $remaining = $this->whatsappService->getRemainingDailyNotifications();
            $this->info("📊 Daily notifications remaining: {$remaining}/10");
        }

        return 0;
    }

    private function checkAndNotifyReorderPoint($notifications, $debug = false)
    {
        // Cek apakah masih bisa kirim notifikasi hari ini (limit 10/hari)
        if (!$this->whatsappService->canSendDailyNotification()) {
            $this->error("❌ Daily notification limit reached. Remaining today: 0/10");
            Log::warning("Daily notification limit reached. Remaining today: 0/10");
            return;
        }

        // Filter item yang belum pernah dinotifikasi atau stoknya berubah
        $itemsToNotify = array_filter($notifications, function ($item) {
            return $this->whatsappService->canSendNotification($item['id'], $item['stok_saat_ini']);
        });

        if ($debug) {
            $this->info("🔍 Items to notify: " . count($itemsToNotify) . "/" . count($notifications));
            foreach ($itemsToNotify as $item) {
                $this->line("   • {$item['nama_barang']} (Stock: {$item['stok_saat_ini']})");
            }
        }

        if (!empty($itemsToNotify)) {
            $this->info("📱 Sending WhatsApp notification for " . count($itemsToNotify) . " items...");

            $sent = $this->whatsappService->sendReorderNotification($itemsToNotify);

            if ($sent) {
                $remaining = $this->whatsappService->getRemainingDailyNotifications();
                $this->info("✅ Reorder notification sent successfully! Remaining today: {$remaining}/10");

                Log::info("Automated reorder notification sent for " . count($itemsToNotify) . " items. Remaining today: {$remaining}/10", [
                    'items' => array_column($itemsToNotify, 'nama_barang'),
                    'stocks' => array_map(function ($item) {
                        return $item['nama_barang'] . ': ' . $item['stok_saat_ini'];
                    }, $itemsToNotify)
                ]);
            } else {
                $this->error("❌ Failed to send reorder notification");
                Log::error("Failed to send automated reorder notification", [
                    'items_count' => count($itemsToNotify)
                ]);
            }
        } else {
            $this->info("ℹ️  All items already notified with current stock levels. No new notifications sent.");
            Log::info("All reorder point items already notified with current stock levels");
        }
    }
}
