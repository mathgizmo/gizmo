<?php

namespace App\Exports;

use App\Application;
use App\ClassApplication;
use App\Progress;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClassStudentsExport implements FromCollection
{
    use Exportable;

    protected $class;
    protected $students;
    protected $assignments = [];
    protected $with_extra = false;
    protected $is_researcher = false;
    protected $collection;

    public function __construct($class, $with_extra = false, $user = null)
    {
        $this->class = $class;
        $this->with_extra = $with_extra;
        if ($with_extra) {
            $this->assignments = Application::whereHas('classes', function ($q1) use ($class) {
                $q1->where('classes.id', $class->id);
            })->where('type', 'assignment')->get();
        }
        $this->students = $class->students()->orderBy('email', 'ASC')->get();
        $output = [];
        $output[] = $this->headings();
        foreach ($this->students as $student) {
            $output[] = $this->map($student);
        }
        $this->collection = collect($output);
        if ($this->class->is_researchable && $user) {
            $this->is_researcher = DB::table('classes_teachers')->where('class_id', $class->id)
                ->where('student_id', $user->id)->where('is_researcher', 1)->count() > 0;
        }
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        $headings = [
            'Email',
            'Name',
            // 'Test Duration Multiplier',
        ];
        if ($this->class->is_researchable) {
            $headings = array_merge($headings, [
                'Consent Read',
                'Element 1 (tracking)',
                'Element 2 (survey)',
                'Element 3 (interview)',
                'Element 4 (numeracy tasks)',
            ]);
        }
        if ($this->with_extra) {
            $headings = array_merge($headings, [
                'Finished Assignments',
                'Finished Tests',
            ]);
        }
        return $headings;

    }

    public function map($student): array
    {
        $row = [
            $student->email,
            $student->first_name . ' ' . $student->last_name,
        ];
        if ($this->class->is_researchable) {
            $row = array_merge($row, [
                $student->pivot->is_consent_read ? 'Yes' : 'No',
                $student->pivot->is_consent_read ? ($student->pivot->is_element1_accepted ? 'Yes' : 'No') : 'N/A',
                $student->pivot->is_consent_read ? ($student->pivot->is_element2_accepted ? 'Yes' : 'No') : 'N/A',
                $student->pivot->is_consent_read ? ($student->pivot->is_element3_accepted ? 'Yes' : 'No') : 'N/A',
                $student->pivot->is_consent_read ? ($student->pivot->is_element4_accepted ? 'Yes' : 'No') : 'N/A',
            ]);
        }
        if ($this->with_extra) {
            $class_id = $this->class->id;
            if ($this->is_researcher && !$student->pivot->is_element1_accepted) {
                array_push($row, 'N/A');
                array_push($row, 'N/A');
            } else {
                $assignments_finished_count = 0;
                foreach ($this->assignments as $app) {
                    $class_data = $app->getClassRelatedData($class_id);
                    if ($class_data->is_for_selected_students) {
                        if (DB::table('classes_applications_students')
                                ->where('class_app_id', $class_data->id)
                                ->where('student_id', $student->id)->count() < 1) {
                            continue;
                        }
                    }
                    $app->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $app->id)
                            ->where('student_id', $student->id)->count() > 0;
                    if ($app->is_completed) {
                        $assignments_finished_count++;
                    }
                }
                array_push($row, $assignments_finished_count);
                $tests_finished_count = ClassApplication::where('class_id', $class_id)
                    ->whereHas('classApplicationStudents', function ($q) use ($student) {
                        $q->where('student_id', $student->id)
                            ->whereHas('testAttempts', function ($q) {
                                $q->whereNotNull('end_at');
                            });
                    })
                    ->select('classes_applications.id')->distinct()->count();
                array_push($row, $tests_finished_count);
            }
        }
        return $row;
    }
}
