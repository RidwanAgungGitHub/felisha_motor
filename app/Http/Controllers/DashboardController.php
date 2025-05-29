<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\SafetyStock;
use App\Models\ReorderPoint;
use App\Services\WhatsAppNotificationService;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppNotificationService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        // Get total barang
        $totalBarang = Barang::count();

        // Get total stok
        $totalStok = Barang::sum('stok');

        // Get barang yang memerlukan perhatian (reorder point atau safety stock)
        $barangs = Barang::orderBy('nama_barang')->get();
        $notifications = [];
        $lowStockItems = 0;

        foreach ($barangs as $barang) {
            // Get latest reorder point data
            $reorderPoint = ReorderPoint::where('barang_id', $barang->id)
                ->latest()
                ->first();

            // Check status dan buat notifikasi - HANYA berdasarkan reorder point
            $status = 'Normal';
            $statusClass = 'success';
            $priority = 0;

            // Hanya cek reorder point sebagai patokan notifikasi
            if ($reorderPoint && $barang->stok <= $reorderPoint->hasil) {
                $status = 'Reorder Point';
                $statusClass = 'warning';
                $priority = 1;
                $lowStockItems++;

                // Hanya tambahkan ke notifikasi jika mencapai reorder point
                $notifications[] = [
                    'id' => $barang->id,
                    'nama_barang' => $barang->nama_barang,
                    'merek' => $barang->merek,
                    'stok_saat_ini' => $barang->stok,
                    'satuan' => $barang->satuan,
                    'reorder_point' => $reorderPoint ? $reorderPoint->hasil : null,
                    'status' => $status,
                    'status_class' => $statusClass,
                    'priority' => $priority,
                    'created_at' => $barang->updated_at
                ];
            }
        }

        // Urutkan notifikasi berdasarkan stok terendah (karena semua prioritas sama)
        usort($notifications, function ($a, $b) {
            return $a['stok_saat_ini'] <=> $b['stok_saat_ini'];
        });

        // HAPUS BAGIAN INI - Karena sekarang sudah otomatis via scheduler
        // $this->checkAndNotifyReorderPoint($notifications);

        // Ambil hanya 10 notifikasi teratas untuk dashboard
        $notifications = array_slice($notifications, 0, 10);

        // Get all suppliers for the modal dropdown
        $suppliers = Supplier::orderBy('nama')->get();

        // Get all barangs for the modal dropdown
        $allBarangs = Barang::orderBy('nama_barang')->get();

        // Get selected barang if any (for pre-selecting in modal)
        $selectedBarang = null;
        $showReorderAlert = false;

        if (request()->has('barang_id')) {
            $selectedBarang = Barang::find(request('barang_id'));

            if ($selectedBarang) {
                // Check if this barang has reached reorder point
                $reorderPoint = ReorderPoint::where('barang_id', $selectedBarang->id)
                    ->latest()
                    ->first();

                if ($reorderPoint && $selectedBarang->stok <= $reorderPoint->hasil) {
                    $showReorderAlert = true;
                }
            }
        }

        // Default values for the form
        $defaultValues = [
            'harga' => '',
            'jumlah' => '',
            'tanggal_beli' => date('Y-m-d'),
            'estimasi_datang' => date('Y-m-d', strtotime('+3 days')),
            'status' => 'pending'
        ];

        return view('dashboard', compact(
            'totalBarang',
            'totalStok',
            'lowStockItems',
            'notifications',
            'barangs',
            'suppliers',
            'allBarangs',
            'selectedBarang',
            'showReorderAlert',
            'defaultValues'
        ));
    }

    private function checkAndNotifyReorderPoint($notifications)
    {
        // Cek apakah masih bisa kirim notifikasi hari ini (limit 10/hari)
        if (!$this->whatsappService->canSendDailyNotification()) {
            Log::info("Daily notification limit reached. Remaining today: 0/10");
            return;
        }

        // Filter item yang belum pernah dinotifikasi hari ini
        $itemsToNotify = array_filter($notifications, function ($item) {
            return $this->whatsappService->canSendNotification($item['id']);
        });

        if (!empty($itemsToNotify)) {
            $sent = $this->whatsappService->sendReorderNotification($itemsToNotify);

            if ($sent) {
                foreach ($itemsToNotify as $item) {
                    $this->whatsappService->markNotificationSent($item['id']);
                }

                $remaining = $this->whatsappService->getRemainingDailyNotifications();
                Log::info("Reorder notification sent for " . count($itemsToNotify) . " items. Remaining today: {$remaining}/10", [
                    'items' => array_column($itemsToNotify, 'nama_barang')
                ]);
            } else {
                Log::warning("Failed to send reorder notification", [
                    'items_count' => count($itemsToNotify)
                ]);
            }
        }
    }
}
