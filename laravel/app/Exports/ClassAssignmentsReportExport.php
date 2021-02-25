<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClassAssignmentsReportExport implements FromCollection
{

    use Exportable;

    protected $collection;

    protected $class;
    protected $assignments;
    protected $students;

    public function __construct($data)
    {
        $this->class = $data['class'];
        $this->assignments = $data['assignments'];
        $this->students = $data['students'];
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
        foreach ($this->assignments as $assignment) {
            array_push($heading, $assignment['name']);
        }
        return $heading;
    }

    public function map($student): array
    {
        $row = [
            $student->student_email,
        ];
        foreach ($this->assignments as $assignment) {
            $app_id = $assignment['id'];
            $data = 'N/A';
            if ($student->data->{$app_id}) {
                $app_data = $student->data->{$app_id};
                $status = $app_data->status;
                $data = $status == 'completed' ? 'Completed' : ($status == 'overdue' ? 'Overdue' : ($status == 'progress' ?  'In progress' : 'Pending'));
                if ($status != 'pending') {
                    $data .= ' (' .round($app_data->progress * 100, 1) . '%)';
                }
            }
            array_push($row, $data);
        }
        return $row;
    }
}
