<?php

namespace App\Exports;

use App\Models\BarangKeluar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BarangKeluarExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return BarangKeluar::with('barang')
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->latest()
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Merek',
            'Jumlah',
            'Satuan',
            'Harga Satuan',
            'Total Harga',
            'Tanggal',
            'Keterangan'
        ];
    }

    /**
     * @param mixed $barangKeluar
     * @return array
     */
    public function map($barangKeluar): array
    {
        static $no = 1;

        return [
            $no++,
            $barangKeluar->barang->nama_barang,
            $barangKeluar->barang->merek,
            $barangKeluar->jumlah,
            $barangKeluar->barang->satuan,
            'Rp ' . number_format($barangKeluar->barang->harga, 0, ',', '.'),
            'Rp ' . number_format($barangKeluar->total_harga, 0, ',', '.'),
            $barangKeluar->tanggal->format('d/m/Y'),
            $barangKeluar->keterangan ?? '-'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Get the highest row and column
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Style the header row
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style all data cells
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Center align the No column
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal('center');

        // Center align the date column
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal('center');

        return [];
    }
}