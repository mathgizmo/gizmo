<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentClassAssignmentsReportExport implements FromCollection
{

    use Exportable;

    protected $collection;

    protected $assignments;
    protected $data;

    public function __construct($assignments, $data)
    {
        $this->assignments = $assignments;
        $this->data = $data;
        $output = [];
        $output[] = $this->headings();
        foreach ($this->assignments as $assignment) {
            $output[] = $this->map($assignment);
        }
        $this->collection = collect($output);
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'Assignment',
            'Completion rate',
            'Questions correct',
            'Questions attempted',
            'Start date/time',
            'Most recent date/time'
        ];
    }

    public function map($app): array
    {
        $row = [
            $app->name,
        ];
        $progress = 'N/A';
        $questions_correct = 'N/A';
        $questions_attempted = 'N/A';
        $start_datetime = 'N/A';
        $recent_activity_datetime = 'N/A';
        try {
            $app_id = $app->id;
            if (property_exists($this->data, $app_id)) {
                $app_data = $this->data->{$app_id};
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
        return $row;
    }
}
