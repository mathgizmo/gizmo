<?php

namespace App\Exports;

use App\ClassOfStudents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClassTestsReportExport implements FromCollection
{

    use Exportable;

    protected $collection;

    protected $class;
    protected $tests;
    protected $students;

    public function __construct($class, $students, $tests)
    {
        $this->class = $class;
        $this->students = $students;
        $this->tests = $tests;
        $output = [];
        $output[] = $this->headings();
        foreach ($this->students as $student) {
            $output[] = $this->map($student);
        }
        $this->collection = collect($output);
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        $heading = ['Student'];
        foreach ($this->tests as $test) {
            for ($i = 0; $i < $test->attempts; $i++) {
                array_push($heading, $test->name.' (Attempt #'.($i+1).')');
            }
        }
        return $heading;
    }

    public function map($student): array
    {
        $row = [$student->email];
        foreach ($this->tests as $test) {
            $stud_data_collection = collect($test->students);
            $stud_data = $stud_data_collection->where('email', $student->email)->first();
            $attempts = $stud_data ? $stud_data->attempts : null;
            for ($i = 0; $i < $test->attempts; $i++) {
                $attempt = $attempts && array_key_exists($i, $attempts) ? $attempts[$i] : null;
                $data = '';
                if ($stud_data && $attempts && $attempt) {
                    if ($attempt->questions_count) {
                        $data = round($attempt->mark * 100) . '%';
                        $data   .= ' (' . round($attempt->mark*$attempt->questions_count) . '/' .$attempt->questions_count .')';
                    }
                }
                array_push($row, $data);
            }
        }
        return $row;
    }
}
