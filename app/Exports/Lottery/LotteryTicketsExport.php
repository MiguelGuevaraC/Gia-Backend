<?php

namespace App\Exports\Lottery;

use App\Http\Resources\LotteryTicketExcelResource;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{FromCollection, WithStyles, WithTitle};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LotteryTicketsExport implements FromCollection, WithStyles, WithTitle
{
    protected Collection $data;
    protected string $name_event;
    protected Collection $mapped;

    public function __construct(Collection $data, string $name_event = 'Reporte')
    {
        $this->data = $data;
        $this->name_event = $name_event;
        $this->mapped = collect();
    }

    public function collection(): Collection
    {
        if ($this->data->isEmpty()) return collect();

        $this->mapped = $this->data
            ->sortByDesc('id')
            ->map(fn($item) => (new LotteryTicketExcelResource($item))->toArray(request()))
            ->values();

        return collect([array_keys($this->mapped->first())])
            ->concat($this->mapped->map(fn($row) => array_values($row)));
    }

    public function title(): string
    {
        return $this->name_event;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => 'FFEEEEEE'],
            ],
        ]);

        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => [
                'borderStyle' => 'thin',
                'color' => ['argb' => 'FFCCCCCC'],
            ]],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
