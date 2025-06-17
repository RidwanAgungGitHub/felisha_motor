<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\BarangKeluar;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Carbon\Carbon;
use Termwind\Components\Dd;

class BarangKeluarImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    use Importable;

    private $errors = [];
    private $successCount = 0;
    private $failedCount = 0;

    public function collection(Collection $collection)
    {
        DB::beginTransaction();
// dd($collection->toArray());
        try {
            foreach ($collection as $index => $row) {
                $rowNumber = $index + 2; // +2 karena index dimulai dari 0 dan ada header row

                try {
                    // Skip jika row kosong
                    if ($this->isRowEmpty($row)) {
                        continue;
                    }

                    // Validasi dan cari barang berdasarkan nama dan merek
                    $namaBarang = trim($row['nama_barang'] ?? '');
                    $merek = trim($row['merek'] ?? '');

                    if (empty($namaBarang) || empty($merek)) {
                        $this->errors[] = "Baris {$rowNumber}: Nama barang dan merek tidak boleh kosong";
                        $this->failedCount++;
                        continue;
                    }

                    $barang = Barang::where('nama_barang', 'LIKE', '%' . $namaBarang . '%')
                                   ->where('merek', 'LIKE', '%' . $merek . '%')
                                   ->first();

                    if (!$barang) {
                        $this->errors[] = "Baris {$rowNumber}: Barang '{$namaBarang} - {$merek}' tidak ditemukan";
                        $this->failedCount++;
                        continue;
                    }

                    // Validasi jumlah
                    $jumlah = $this->parseNumber($row['jumlah'] ?? 0);
                    if ($jumlah <= 0) {
                        $this->errors[] = "Baris {$rowNumber}: Jumlah harus lebih dari 0";
                        $this->failedCount++;
                        continue;
                    }

                    // Validasi stok
                    if ($barang->stok < $jumlah) {
                        $this->errors[] = "Baris {$rowNumber}: Stok tidak mencukupi untuk '{$barang->nama_barang}'. Stok tersedia: {$barang->stok}, diminta: {$jumlah}";
                        $this->failedCount++;
                        continue;
                    }

                    // Parse tanggal
                    $tanggal = $this->parseDate($row['tanggal'] ?? '');
                    if (!$tanggal) {
                        $this->errors[] = "Baris {$rowNumber}: Format tanggal tidak valid '{$row['tanggal']}'. Gunakan format DD/MM/YYYY";
                        $this->failedCount++;
                        continue;
                    }

                    // Hitung total harga berdasarkan harga barang yang ada di database
                    $totalHarga = $barang->harga * $jumlah;

                    // Validasi total harga dari Excel (opsional, untuk cross-check)
                    if (isset($row['total_harga']) && !empty($row['total_harga'])) {
                        $totalHargaExcel = $this->parsePrice($row['total_harga']);
                        if ($totalHargaExcel && abs($totalHarga - $totalHargaExcel) > 1) {
                            $this->errors[] = "Baris {$rowNumber}: Total harga tidak sesuai. Dihitung: Rp " . number_format($totalHarga, 0, ',', '.') . ", Excel: Rp " . number_format($totalHargaExcel, 0, ',', '.');
                            // Tetap lanjut, tapi beri peringatan
                        }
                    }

                    // Simpan data barang keluar
                    BarangKeluar::create([
                        'barang_id' => $barang->id,
                        'jumlah' => $jumlah,
                        'tanggal' => $tanggal,
                        'total_harga' => $totalHarga,
                        'keterangan' => 'Import Excel - ' . now()->format('d/m/Y H:i'),
                    ]);

                    // Update stok barang
                    $barang->decrement('stok', $jumlah);

                    $this->successCount++;

                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$rowNumber}: Error - " . $e->getMessage();
                    $this->failedCount++;
                    Log::error("Error import barang keluar baris {$rowNumber}: " . $e->getMessage());
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error saat import barang keluar: " . $e->getMessage());
            throw new \Exception("Gagal melakukan import: " . $e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            'nama_barang' => 'required|string',
            'merek' => 'required|string',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_barang.required' => 'Kolom Nama Barang wajib diisi',
            'nama_barang.string' => 'Kolom Nama Barang harus berupa teks',
            'merek.required' => 'Kolom Merek wajib diisi',
            'merek.string' => 'Kolom Merek harus berupa teks',
            'jumlah.required' => 'Kolom Jumlah wajib diisi',
            'jumlah.numeric' => 'Kolom Jumlah harus berupa angka',
            'jumlah.min' => 'Kolom Jumlah minimal 1',
            'tanggal.required' => 'Kolom Tanggal wajib diisi',
        ];
    }

    private function isRowEmpty($row)
    {
        return empty(trim($row['nama_barang'] ?? '')) &&
               empty(trim($row['merek'] ?? '')) &&
               empty(trim($row['jumlah'] ?? '')) &&
               empty(trim($row['tanggal'] ?? ''));
    }

    private function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        // Remove non-numeric characters except comma and dot
        $cleanValue = preg_replace('/[^\d,.]/', '', $value);
        $cleanValue = str_replace(',', '', $cleanValue);

        return is_numeric($cleanValue) ? (int) $cleanValue : 0;
    }

    private function parseDate($dateString)
    {
        try {
            if (empty($dateString)) {
                return null;
            }

            // Jika berupa angka (serial date Excel)
            if (is_numeric($dateString)) {
                // Excel base date: 1 Jan 1900 (offset -2 karena Excel bug leap year 1900)
                $base = \DateTime::createFromFormat('Y-m-d', '1900-01-01');
                $base->modify('+' . ((int)$dateString - 2) . ' days');
                return $base->format('Y-m-d');
            }

            // Jika berupa string, coba parse dengan beberapa format umum
            $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'd-m-y', 'm/d/Y', 'Y/m/d'];
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, trim($dateString));
                if ($date !== false && $date->format($format) === trim($dateString)) {
                    return $date->format('Y-m-d');
                }
            }

            // Fallback: gunakan Carbon parse
            $carbonDate = Carbon::parse(trim($dateString));
            return $carbonDate->format('Y-m-d');

        } catch (\Exception $e) {
            Log::warning("Gagal parsing tanggal: {$dateString}. Error: " . $e->getMessage());
            return null;
        }
    }

    private function parsePrice($priceString)
    {
        if (is_numeric($priceString)) {
            return (float) $priceString;
        }

        // Hapus karakter non-angka, kecuali titik dan koma
        $cleanPrice = preg_replace('/[^\d]/', '', $priceString);

        return is_numeric($cleanPrice) ? (float) $cleanPrice : null;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailedCount()
    {
        return $this->failedCount;
    }

    public function getSummary()
    {
        return [
            'success_count' => $this->successCount,
            'failed_count' => $this->failedCount,
            'total_processed' => $this->successCount + $this->failedCount,
            'errors' => $this->errors
        ];
    }
}