<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\SafetyStock;
use App\Models\ReorderPoint;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportController extends Controller
{
    public function inventoryStatus(Request $request)
    {
        // Get all active products
        $barangs = Barang::orderBy('nama_barang')->get();

        $inventoryStatus = [];

        foreach ($barangs as $barang) {
            // Get latest safety stock and reorder point data for this product if exists
            $safetyStock = SafetyStock::where('barang_id', $barang->id)
                ->latest()
                ->first();

            $reorderPoint = ReorderPoint::where('barang_id', $barang->id)
                ->latest()
                ->first();

            // Calculate inventory status
            $status = 'Normal';
            $statusClass = 'success';

            if ($reorderPoint && $barang->stok <= $reorderPoint->hasil) {
                $status = 'Reorder Point';
                $statusClass = 'warning';
            }

            if ($safetyStock && $barang->stok <= $safetyStock->hasil) {
                $status = 'Safety Stock';
                $statusClass = 'danger';
            }

            // Add to inventory status array
            $inventoryStatus[] = [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'merek' => $barang->merek,
                'stok' => $barang->stok,
                'satuan' => $barang->satuan,
                'safety_stock' => $safetyStock ? $safetyStock->hasil : null,
                'reorder_point' => $reorderPoint ? $reorderPoint->hasil : null,
                'period' => $reorderPoint ? $reorderPoint->period : null,
                'status' => $status,
                'status_class' => $statusClass,
            ];
        }

        return view('reports.inventory_status', compact('inventoryStatus'));
    }

    public function exportInventoryStatus()
    {
        // Get inventory status data
        $barangs = Barang::orderBy('nama_barang')->get();
        $inventoryStatus = [];

        foreach ($barangs as $barang) {
            $safetyStock = SafetyStock::where('barang_id', $barang->id)
                ->latest()
                ->first();

            $reorderPoint = ReorderPoint::where('barang_id', $barang->id)
                ->latest()
                ->first();

            $status = 'Normal';
            if ($reorderPoint && $barang->stok <= $reorderPoint->hasil) {
                $status = 'Reorder Point';
            }
            if ($safetyStock && $barang->stok <= $safetyStock->hasil) {
                $status = 'Safety Stock';
            }

            $inventoryStatus[] = [
                'nama_barang' => $barang->nama_barang,
                'merek' => $barang->merek,
                'stok' => $barang->stok,
                'satuan' => $barang->satuan,
                'safety_stock' => $safetyStock ? $safetyStock->hasil : '-',
                'reorder_point' => $reorderPoint ? $reorderPoint->hasil : '-',
                'period' => $reorderPoint ? $reorderPoint->period : '-',
                'status' => $status,
            ];
        }

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Merek');
        $sheet->setCellValue('D1', 'Stok Saat Ini');
        $sheet->setCellValue('E1', 'Safety Stock');
        $sheet->setCellValue('F1', 'Reorder Point');
        $sheet->setCellValue('G1', 'Period');
        $sheet->setCellValue('H1', 'Status');

        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E9ECEF',
                ],
            ],
        ];

        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Fill data
        $row = 2;
        foreach ($inventoryStatus as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['nama_barang']);
            $sheet->setCellValue('C' . $row, $item['merek']);
            $sheet->setCellValue('D' . $row, $item['stok'] . ' ' . $item['satuan']);
            $sheet->setCellValue('E' . $row, is_numeric($item['safety_stock']) ? $item['safety_stock'] . ' ' . $item['satuan'] : '-');
            $sheet->setCellValue('F' . $row, is_numeric($item['reorder_point']) ? $item['reorder_point'] . ' ' . $item['satuan'] : '-');
            $sheet->setCellValue('G' . $row, $item['period']);
            $sheet->setCellValue('H' . $row, $item['status']);

            // Apply cell style
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            $row++;
        }

        // Auto fit column widths
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set filename
        $filename = 'Laporan_Status_Inventori_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Create Excel file and send for download
        $writer = new Xlsx($spreadsheet);

        // Redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function printInventoryStatus()
    {
        // Get all active products
        $barangs = Barang::orderBy('nama_barang')->get();

        $inventoryStatus = [];

        foreach ($barangs as $barang) {
            // Get latest safety stock and reorder point data for this product if exists
            $safetyStock = SafetyStock::where('barang_id', $barang->id)
                ->latest()
                ->first();

            $reorderPoint = ReorderPoint::where('barang_id', $barang->id)
                ->latest()
                ->first();

            // Calculate inventory status
            $status = 'Normal';
            $statusClass = 'success';

            if ($reorderPoint && $barang->stok <= $reorderPoint->hasil) {
                $status = 'Reorder Point';
                $statusClass = 'warning';
            }

            if ($safetyStock && $barang->stok <= $safetyStock->hasil) {
                $status = 'Safety Stock';
                $statusClass = 'danger';
            }

            // Add to inventory status array
            $inventoryStatus[] = [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'merek' => $barang->merek,
                'stok' => $barang->stok,
                'satuan' => $barang->satuan,
                'safety_stock' => $safetyStock ? $safetyStock->hasil : null,
                'reorder_point' => $reorderPoint ? $reorderPoint->hasil : null,
                'period' => $reorderPoint ? $reorderPoint->period : null,
                'status' => $status,
                'status_class' => $statusClass,
            ];
        }

        return view('reports.print.inventory_status', compact('inventoryStatus'));
    }
}
