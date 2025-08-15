<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FinancialExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $financialData;

    public function __construct(Collection $financialData)
    {
        $this->financialData = $financialData;
    }

    public function collection()
    {
        return $this->financialData;
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'الإيرادات',
            'المصروفات',
            'الصافي',
        ];
    }

    public function map($row): array
    {
        return [
            $row['date']->format('Y-m-d'),
            number_format($row['revenue'], 2),
            number_format($row['expenses'], 2),
            number_format($row['net'], 2),
        ];
    }

    public function title(): string
    {
        return 'التقرير المالي';
    }
}