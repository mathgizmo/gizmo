<?php

namespace App\Exports;

use App\ClassOfStudents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentClassTestsReportExport implements FromCollection
{

    use Exportable;

    protected $collection;

    protected $tests;
    protected $max_attempts;

    public function __construct($tests, $max_attempts)
    {
        $this->tests = $tests;
        $this->max_attempts = $max_attempts;
        $output = [];
        $output[] = $this->headings();
        foreach ($this->tests as $test) {
            $output[] = $this->map($test);
        }
        $this->collection = collect($output);
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        $heading = [
            'Test',
            'First attempt date/time',
            'Most recent attempt date/time'
        ];
        for ($i = 0; $i < $this->max_attempts; $i++) {
            array_push($heading, 'Attempt #'.($i+1));
        }
        return $heading;
    }

    public function map($test): array
    {
        $first_attempt = $test->attempts->sortBy('start_at')->first();
        $last_attempt = $test->attempts->sortByDesc('start_at')->first();
        $row = [
            $test->name,
            $first_attempt ? $first_attempt->start_at : null,
            $last_attempt ? $last_attempt->start_at : null,
        ];
        foreach ($test->attempts as $attempt) {
            $data = '';
            if ($attempt->questions_count) {
                $data = round($attempt->mark * 100) . '%';
                $data   .= ' (' . round($attempt->mark*$attempt->questions_count) . '/' .$attempt->questions_count .')';
            }
            array_push($row, $data);
        }
        return $row;
    }
}
