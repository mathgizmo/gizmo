<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClassTestsReportExport implements FromCollection, WithHeadings, WithMapping
{

    use Exportable;

    protected $collection;

    public function __construct($class_id)
    {
        $this->collection = [];
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'Name',
        ];
    }

    public function map($item): array
    {
        return [
            $item->name,
        ];
    }
}
