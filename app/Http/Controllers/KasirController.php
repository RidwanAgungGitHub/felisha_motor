<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Support\Facades\Http;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KasirController extends Controller
{
    // Fungsi untuk menampilkan halaman kasir - TANPA LAYOUT UTAMA
    public function index(Request $request)
    {
        $data = Barang::where('stok', '>', 0)->get();

        // Return view khusus kasir tanpa extends layout utama
        return view('kasir.index', compact('data'));
    }

    // Fungsi untuk menambahkan item ke keranjang
    public function addToCart(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
        ]);

        $barang_id = $request->barang_id;
        $barang = Barang::findOrFail($barang_id);

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan');
        }

        $cart = session()->get('cart', []);

        // Jika barang sudah ada di keranjang, tambahkan jumlahnya
        if (isset($cart[$barang_id])) {
            // Periksa apakah jumlah yang ditambahkan melebihi stok
            if ($cart[$barang_id]['jumlah'] + 1 > $barang->stok) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $barang->stok);
            }
            $cart[$barang_id]['jumlah']++;
        } else {
            // Jika belum ada, tambahkan barang baru ke keranjang
            $cart[$barang_id] = [
                'id' => $barang->id,
                'kode' => $barang->id, // Menggunakan ID sebagai kode
                'nama_barang' => $barang->nama_barang . ' - ' . $barang->merek,
                'merek' => $barang->merek,
                'harga' => $barang->harga,
                'jumlah' => 1,
                'stok_tersedia' => $barang->stok
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    // Fungsi untuk mengubah jumlah item di keranjang
    public function updateCartQty(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'jumlah' => 'required|integer|min:1',
        ]);

        $id = $request->id;
        $jumlah = $request->jumlah;

        $cart = session()->get('cart');

        if ($jumlah <= 0) {
            return $this->removeFromCart($request);
        }

        if (isset($cart[$id])) {
            // Periksa apakah jumlah yang diupdate melebihi stok
            if ($jumlah > $cart[$id]['stok_tersedia']) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $cart[$id]['stok_tersedia']);
            }

            $cart[$id]['jumlah'] = $jumlah;
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Jumlah barang berhasil diupdate');
    }

    // Fungsi untuk menghapus item dari keranjang
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $id = $request->id;
        $cart = session()->get('cart');

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Barang berhasil dihapus dari keranjang');
    }

    // Fungsi untuk membersihkan keranjang
    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Keranjang berhasil dikosongkan');
    }

    // Fungsi untuk checkout
    public function checkout(Request $request)
    {
        $request->validate([
            'tunai' => 'required|numeric|min:0',
            'nama_pelanggan' => 'nullable|string|max:255',
            'no_whatsapp' => 'nullable|string|max:20',
        ]);

        $cart = session()->get('cart');

        if (!$cart || count($cart) == 0) {
            return redirect()->back()->with('error', 'Keranjang belanja masih kosong');
        }

        // Hitung total transaksi
        $totalTransaksi = 0;
        foreach ($cart as $item) {
            $totalTransaksi += $item['harga'] * $item['jumlah'];
        }

        // Validasi jumlah tunai
        if ($request->tunai < $totalTransaksi) {
            return redirect()->back()->with('error', 'Jumlah tunai kurang dari total belanja');
        }

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . Str::random(5);

        DB::beginTransaction();

        try {
            foreach ($cart as $id => $item) {
                $barang = Barang::findOrFail($id);

                if ($barang->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}");
                }

                $totalHarga = $barang->harga * $item['jumlah'];

                BarangKeluar::create([
                    'barang_id' => $id,
                    'jumlah' => $item['jumlah'],
                    'tanggal' => now(),
                    'total_harga' => $totalHarga,
                    'keterangan' => 'Penjualan kasir - ' . $invoiceNumber .
                        ($request->nama_pelanggan ? ' - Pelanggan: ' . $request->nama_pelanggan : ''),
                ]);

                $barang->stok -= $item['jumlah'];
                $barang->save();
            }

            DB::commit();

            // Simpan informasi checkout untuk halaman struk
            $checkout = [
                'invoice_number' => $invoiceNumber,
                'tanggal' => now()->format('Y-m-d H:i:s'),
                'nama_pelanggan' => $request->nama_pelanggan ?? 'Pelanggan Umum',
                'no_whatsapp' => $request->no_whatsapp,
                'items' => $cart,
                'total' => $totalTransaksi,
                'tunai' => $request->tunai,
                'kembalian' => $request->tunai - $totalTransaksi
            ];

            session()->put('checkout', $checkout);
            session()->forget('cart');

            return redirect()->route('kasir.struk')->with('success', 'Pembayaran berhasil diproses!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Fungsi untuk mengirim struk via WhatsApp - DIPERBAIKI
    public function kirimPesanWhatsapp(Request $request)
    {
        try {
            $request->validate([
                'no_whatsapp' => 'required|string',
                'nama_pelanggan' => 'required|string',
                'total' => 'required|numeric',
                'tunai' => 'required|numeric',
                'kembalian' => 'required|numeric',
                'invoice_number' => 'required|string',
                'items' => 'required|array'
            ]);

            // Format nomor WhatsApp
            $nomorWA = $request->no_whatsapp;
            if (substr($nomorWA, 0, 1) === '0') {
                $nomorWA = '62' . substr($nomorWA, 1);
            } elseif (substr($nomorWA, 0, 2) !== '62') {
                $nomorWA = '62' . $nomorWA;
            }

            // Buat pesan struk yang lebih rapi
            $pesan = "*STRUK PEMBAYARAN*\n";
            $pesan .= "*Falisa Inventory*\n";
            $pesan .= "================================\n";
            $pesan .= "Invoice: " . $request->invoice_number . "\n";
            $pesan .= "Tanggal: " . now()->format('d/m/Y H:i:s') . "\n";
            $pesan .= "Kasir: " . Auth::user()->name . "\n";
            $pesan .= "Pelanggan: " . $request->nama_pelanggan . "\n";
            $pesan .= "================================\n";
            $pesan .= "*DETAIL PEMBELIAN:*\n";

            // Tambahkan detail items
            foreach ($request->items as $item) {
                $subtotal = $item['harga'] * $item['jumlah'];
                $pesan .= $item['nama_barang'] . "\n";
                $pesan .= $item['jumlah'] . " x Rp " . number_format($item['harga'], 0, ',', '.') . " = Rp " . number_format($subtotal, 0, ',', '.') . "\n\n";
            }

            $pesan .= "================================\n";
            $pesan .= "Subtotal: Rp " . number_format($request->total, 0, ',', '.') . "\n";
            $pesan .= "Tunai: Rp " . number_format($request->tunai, 0, ',', '.') . "\n";
            $pesan .= "Kembalian: Rp " . number_format($request->kembalian, 0, ',', '.') . "\n";
            $pesan .= "================================\n";
            $pesan .= "Terima kasih atas kunjungan Anda!\n";
            $pesan .= "Barang yang sudah dibeli tidak dapat ditukar/dikembalikan";

            // Kirim via API Fonnte
            $response = Http::withHeaders([
                'Authorization' => 'LqbEgMK6bpw9aKyZjp9q' // Ganti dengan API key yang benar
            ])->post('https://api.fonnte.com/send', [
                'target' => $nomorWA,
                'message' => $pesan,
                'countryCode' => '62'
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['status']) && $responseData['status'] == true) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Struk berhasil dikirim ke WhatsApp!'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengirim pesan: ' . ($responseData['reason'] ?? 'Unknown error')
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal terhubung ke server WhatsApp'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fungsi untuk menampilkan struk - TANPA LAYOUT UTAMA
    public function struk()
    {
        $checkout = session()->get('checkout');

        if (!$checkout) {
            return redirect()->route('kasir')->with('error', 'Data transaksi tidak ditemukan');
        }

        // Return view khusus struk tanpa layout utama
        return view('kasir.struk', compact('checkout'));
    }

    // Fungsi untuk menghitung kembalian (diakses dengan GET)
    public function hitungKembalian(Request $request)
    {
        $request->validate([
            'tunai' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $tunai = $request->tunai;
        $total = $request->total;
        $kembalian = $tunai - $total;

        return redirect()->route('kasir')->with('kembalian', $kembalian);
    }

    // Fungsi cetak laporan - KHUSUS KASIR
    public function cetakLaporan(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');

        // Hanya tampilkan transaksi yang dilakukan oleh kasir ini
        $data = BarangKeluar::with('barang')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->where('keterangan', 'like', '%Penjualan kasir%')
            ->latest()
            ->get();

        // Hitung ringkasan laporan
        $laporan = [
            'jumlah_transaksi' => $data->count(),
            'total_pendapatan' => $data->sum('total_harga'),
            'jumlah_barang' => $data->sum('jumlah'),
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir,
            'kasir' => Auth::user()->name
        ];

        // Return view laporan khusus kasir tanpa layout utama
        return view('kasir.laporan', compact('data', 'laporan'));
    }
}
