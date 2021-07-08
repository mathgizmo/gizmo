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
            $name = $assignment['name'];
            array_push($heading, $name.': completion rate');
            array_push($heading, $name.': questions correct');
            array_push($heading, $name.': questions attempted');
            array_push($heading, $name.': start date/time');
            array_push($heading, $name.': most recent date/time');
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
            $progress = 'N/A';
            $questions_correct = 'N/A';
            $questions_attempted = 'N/A';
            $start_datetime = 'N/A';
            $recent_activity_datetime = 'N/A';
            try {
                if (property_exists($student->data, $app_id)) {
                    $app_data = $student->data->{$app_id};
                    $status = $app_data->status;
                    if ($status != 'pending') {
                        $progress = round($app_data->progress * 100, 1) . '%';
                    }
                    $questions_correct = $app_data->questions_correct;
                    $questions_attempted = $app_data->questions_attempted;
                    $start_datetime = $app_data->start_datetime;
                    $recent_activity_datetime = $app_data->recent_activity_datetime;
                }
            } catch (\Exception $e) { }
            array_push($row, $progress);
            array_push($row, $questions_correct);
            array_push($row, $questions_attempted);
            array_push($row, $start_datetime);
            array_push($row, $recent_activity_datetime);
        }
        return $row;
    }
}
