<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassApplication;
use App\ClassOfStudents;
use App\Exports\StudentClassAssignmentsReportExport;
use App\Exports\StudentClassTestsReportExport;
use App\Progress;
use App\Student;
use App\StudentTestAttempt;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{

    private $user;

    public function __construct()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
            if (!$this->user) {
                abort(401, 'Unauthorized!');
            }
        } catch (\Exception $e) {
            abort(401, 'Unauthorized!');
        }
    }
    
    public function get()
    {
        $student = $this->user;
        return $this->success([
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'email_new' => $student->email_new ?: null,
            'app_id' => $student->app_id,
            'applications' => Application::whereDoesntHave('teacher')->get(),
            'country_id' => $student->country_id
        ]);
    }

    public function update()
    {
        $student = $this->user;
        $update = [];
        if (request()->has('first_name')) {
            $update['first_name'] = request('first_name');
        }
        if (request()->has('last_name')) {
            $update['last_name'] = request('last_name');
        }
        $send_email_verification = false;
        if (request()->has('email')) {
            $email_new = strtolower(request('email'));
            $update['email_new'] = $email_new != $student->email ? $email_new : null;
            $send_email_verification = $email_new != $student->email && $email_new != $student->email_new;
        }
        if (request()->has('password')) {
            $update['password'] = request('password');
        }
        if (request()->has('country_id')) {
            $update['country_id'] = intval(request('country_id'));
        }
        $validator = Validator::make(
            $update,
            [
                'email' => 'email|max:255|unique:students,email,'.$student->id,
                'email_new' => 'nullable|email|max:255|unique:students,email,'.$student->id,
                'password' => 'nullable|min:6',
            ]
        );
        if ($validator->fails()) {
            return $this->error($validator->messages(), 400);
        }
        if (request()->has('password')) {
            $update['password'] = bcrypt(request('password'));
        }
        Student::find($student->id)->update($update);
        if ($send_email_verification) {
            try {
                (new AuthController())->resendVerificationEmail(request());
            } catch(\Exception $e) { }
        }
        return $this->success('OK.');
    }

    public function getToDos(Request $request) {
        $student = $this->user;
        $items = [];
        if ($student->is_self_study) {
            $classObj = (object) [
                'teacher_id' => null,
                'name' => 'Gizmo',
                'class_type' => 'other',
                'subscription_type' => 'open',
                'invitations' => null
            ];
            foreach (Application::whereDoesntHave('teacher')->where('type', 'assignment')->get() as $item) {
                $item->class = $classObj;
                $item->icon = $item->icon();
                $item->is_completed = $item->isFinished($student->id);
                $item->start_time = null;
                $item->start_date = null;
                $item->due_time = null;
                $item->due_date = null;
                $item->due_at = null;
                $item->is_blocked = false;
                $item->start_at = null;
                $item->duration = $item->duration ? CarbonInterval::seconds($item->duration)->cascade()->forHumans() : null;
                $completed_at = $item->getCompletedDate($student->id);
                $item->completed_at = $completed_at ? Carbon::parse($completed_at)->format('Y-m-d g:i A') : null;
                array_push($items, $item);
            }
        } else {
            $class_id = $request->filled('class_id') ? $request['class_id'] : null;
            $student_classes = $student->classes()->get()->pluck('id')->toArray();
            $class_ids = $class_id && in_array($class_id, $student_classes) ? [$class_id] : $student_classes;
            $apps = ClassApplication::whereIn('class_id', $class_ids)->whereHas('assignment')->get();
            foreach ($apps as $row) {
                if ($row->is_for_selected_students) {
                    if (DB::table('classes_applications_students')->where('class_app_id', $row->id)
                            ->where('student_id', $student->id)->count() < 1) {
                        continue;
                    }
                }
                $item = Application::where('id', $row->app_id)->first();
                $classObj = ClassOfStudents::where('id', $row->class_id)->first();
                if (!$item || !$classObj) {
                    continue;
                }
                $item->class = $classObj;
                $item->class_app_id = $row->id;
                $item->icon = $item->icon();
                $item->is_completed = $item->isFinished($student->id);
                if ($row->due_date) {
                    $due_at = $row->due_time ? $row->due_date.' '.$row->due_time : $row->due_date.' 00:00:00';
                } else {
                    $due_at = null;
                }
                $item->start_time = $row->start_time ?: '00:00:00';
                $item->start_date = $row->start_date;
                $item->due_time = $row->due_time ?: '00:00:00';
                $item->due_date = $row->due_date;
                $item->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
                $now = Carbon::now()->toDateTimeString();
                if ($row->start_date) {
                    $start_at = $row->start_time ? $row->start_date.' '.$row->start_time : $row->start_date.' 00:00:00';
                } else {
                    $start_at = null;
                }
                $completed_at = $item->getCompletedDate($student->id);
                $item->completed_at = $completed_at ? Carbon::parse($completed_at)->format('Y-m-d g:i A') : null;
                $item->is_blocked = $item->is_completed || ($start_at && $now < $start_at) || ($due_at && $now > $due_at);
                $item->start_at = $start_at && $now < $start_at ? Carbon::parse($start_at)->format('Y-m-d g:i A') : null;
                array_push($items, $item);
            }
        }
        return $this->success([
            'items' => array_values(collect($items)->sortBy('due_at')->toArray())
        ]);
    }

    public function getTests(Request $request) {
        $student = $this->user;
        $items = [];
        $class_id = $request->filled('class_id') ? $request['class_id'] : null;
        $student_classes = $student->classes()->get()->pluck('id')->toArray();
        $class_ids = $class_id && in_array($class_id, $student_classes) ? [$class_id] : $student_classes;
        $apps = ClassApplication::whereIn('class_id', $class_ids)->whereHas('test')->get();
        foreach ($apps as $row) {
            $item = Application::where('id', $row->app_id)->first();
            $classObj = ClassOfStudents::where('id', $row->class_id)->first();
            $test_student = DB::table('classes_applications_students')
                ->where('class_app_id', $row->id)
                ->where('student_id', $student->id)
                ->first();
            if (!$item || !$classObj || ($row->is_for_selected_students && !$test_student)) {
                continue;
            }
            $class_student = DB::table('classes_students')
                ->where('class_id', $row->class_id)
                ->where('student_id', $student->id)->first();
            $now = Carbon::now()->toDateTimeString();
            $item->class = $classObj;
            $item->class_app_id = $row->id;
            $item->icon = $item->icon();
            $item->start_time = $row->start_time ?: '00:00:00';
            $item->start_date = $row->start_date;
            if ($row->start_date) {
                $start_at = $row->start_time ? $row->start_date.' '.$row->start_time : $row->start_date.' 00:00:00';
            } else {
                $start_at = null;
            }
            $item->start_at = $start_at && $now < $start_at ? Carbon::parse($start_at)->format('Y-m-d g:i A') : null;
            $item->due_time = $row->due_time ?: '00:00:00';
            $item->due_date = $row->due_date;
            if ($row->due_date) {
                $due_at = $row->due_time ? $row->due_date.' '.$row->due_time : $row->due_date.' 00:00:00';
            } else {
                $due_at = null;
            }
            $item->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
            $duration = $row->duration && $class_student
                ? ($row->duration * $class_student->test_duration_multiply_by)
                : ($row->duration ?: null);
            $item->duration = $duration ? CarbonInterval::seconds($duration)->cascade()->forHumans() : null;
            $item->is_revealed = ($row->password && (!$test_student || !$test_student->is_revealed)) ? false : true;
            $attempts = $test_student ? DB::table('students_test_attempts')
                ->where('test_student_id', $test_student->id)
                ->orderBy('attempt_no', 'ASC')
                ->get() : [];
            $current_attempt = $test_student ? $attempts->whereNull('end_at')->first() : null;
            $attempts_count = count($attempts);
            $max_mark = $attempts_count ? $attempts->sortByDesc('mark')->first()->mark : 0;
            foreach ($attempts as $index => $attempt) {
                $attempt_item = clone $item;
                $attempt_item->total_questions_count = $attempt->questions_count;
                $attempt_item->attempt_id = $attempt->id;
                $attempt_item->attempt_no = $index + 1;
                $attempt_item->mark = $attempt->mark;
                $attempt_item->questions_count = $attempt->questions_count;
                $attempt_item->is_completed = ($attempt->end_at || $attempt->mark) ? true : false;
                $attempt_item->completed_at = $attempt->end_at ? Carbon::parse($attempt->end_at)->format('Y-m-d g:i A') : null;
                $attempt_item->is_blocked = $attempt_item->is_completed || ($start_at && $now < $start_at) || ($due_at && $now > $due_at);
                $attempt_item->in_progress = ($current_attempt && $current_attempt->id == $attempt->id) ? true : false;
                $attempt_item->is_error = $attempt->is_error;
                array_push($items, $attempt_item);
            }
            if ($attempts_count < $row->attempts && !$current_attempt && $max_mark < 0.9999) {
                $item->total_questions_count = $item->getQuestionsCount();
                $item->attempts_remaining = $row->attempts - $attempts_count;
                $item->is_completed = false;
                $item->is_blocked = ($start_at && $now < $start_at) || ($due_at && $now > $due_at);
                array_push($items, $item);
            }
        }
        return $this->success([
            'items' => array_values(collect($items)->sortBy('due_at')->toArray())
        ]);
    }

    public function revealTest(Request $request, $test_id) {
        $student = $this->user;
        if (!request()->has('password')) {
            return $this->error('Password is required!', 400);
        }
        $app = ClassApplication::whereIn('class_id', $student->classes()->get()->pluck('id')->toArray())
            ->whereHas('test')->where('password', trim(request('password')))
            ->where('id', $test_id)
            ->first();
        if (!$app) {
            return $this->error('Wrong Password!', 400);
        }
        $test = $app->test()->first();
        $class_app_stud = DB::table('classes_applications_students')
            ->where('class_app_id', $app->id)
            ->where('student_id', $student->id)->first();
        if (($app->is_for_selected_students && !$class_app_stud) || !$test) {
            return $this->error('Test Not Found!', 404);
        }
        if ($class_app_stud) {
            DB::table('classes_applications_students')->where('class_app_id', $app->id)
                ->where('student_id', $student->id)->update([
                    'is_revealed' => true
                ]);
        } else {
            DB::table('classes_applications_students')->insert([
                'class_app_id' => $app->id,
                'student_id' => $student->id,
                'is_revealed' => true
            ]);
        }
        $class_student = DB::table('classes_students')
            ->where('class_id', $app->class_id)
            ->where('student_id', $student->id)->first();
        $duration = $app->duration && $class_student
            ? ($app->duration * $class_student->test_duration_multiply_by)
            : ($app->duration ?: null);
        $test_duration = $duration ? CarbonInterval::seconds($duration)->cascade()->forHumans() : null;
        return $this->success([
            'class_app_id' => $app->id,
            'name' => $test->name,
            'duration' => $test_duration,
            'total_questions_count' => $test->getQuestionsCount()
        ], 200);
    }

    public function changeApplication() {
        $student = $this->user;
        if (request()->has('app_id')) {
            $user = Student::find($student->id);
            $user->app_id = request('app_id');
            $user->save();
            return $this->success('OK.');
        } else {
            return $this->error('Error.', 400);
        }
    }

    public function getClasses() {
        $student = $this->user;
        $my_classes = ClassOfStudents::whereHas('classStudents', function ($q) use ($student) {
            $q->where('student_id', $student->id)->where('is_unsubscribed', '<>', true);
        })->orderBy('name')->get();
        foreach ($my_classes as $item) {
            $teacher = Student::where('id', $item->teacher_id)->first();
            $item->teacher = $teacher ? $teacher->first_name.' '.$teacher->last_name : '';
            $item->teacher_email = $teacher ? $teacher->email : '';
            $item->teachers = $item->teachers()->get(['students.id', 'students.email', 'students.first_name', 'students.last_name']);
        }
        $available_classes = ClassOfStudents::where(function ($q1) use ($student) {
            $q1->where('subscription_type', 'open')->orWhere(function ($q2) use ($student) {
                $q2->where('subscription_type', 'assigned')->where('invitations', 'LIKE', '%'.$student->email.'%');
            });
        })->whereNotIn('id', $my_classes->pluck('id')->toArray())->orderBy('name')->get()->keyBy('id');
        foreach ($available_classes as $item) {
            if ($item->subscription_type == 'assigned') {
                $emails = explode(',', strtolower(str_replace(' ', '', preg_replace( "/;|\n/", ',', $item->invitations))));
                $emails = array_map(function ($email) {
                    return str_replace('"', '', trim($email));
                }, $emails);
                if (!in_array($student->email, $emails)) {
                    $available_classes->forget($item->id);
                    continue;
                }
            }
            $teacher = Student::where('id', $item->teacher_id)->first();
            $item->teacher = $teacher ? $teacher->first_name.' '.$teacher->last_name : '';
            $item->teacher_email = $teacher ? $teacher->email : '';
            $item->teachers = $item->teachers()->get(['students.id', 'students.email', 'students.first_name', 'students.last_name']);
        }
        return $this->success([
            'my_classes' => array_values($my_classes->toArray()),
            'available_classes' => array_values($available_classes->toArray()),
        ]);
    }

    public function subscribeClass($class_id) {
        $student = $this->user;
        $class = ClassOfStudents::where('id', $class_id)->orWhere('key', $class_id)->first();
        if (!$class) {
            return $this->error('Classroom not exists!', 404);
        }
        $exists = DB::table('classes_students')->where('class_id', $class->id)
            ->where('student_id', $student->id)->first();
        if (!$exists) {
            switch ($class->subscription_type) {
                default:
                case 'open':
                    break;
                case 'assigned':
                    $emails = explode(',', strtolower(str_replace(' ', '', preg_replace( "/;|\n/", ',', $class->invitations))));
                    $emails = array_map(function ($email) {
                        return str_replace('"', '', trim($email));
                    }, $emails);
                    if (!in_array($student->email, $emails)) {
                        return $this->error('The classroom is available only for assigned students!', 400);
                    }
                    break;
                case 'invitation':
                    if ($class->key !== $class_id) {
                        return $this->error('Invalid classroom URL!', 404);
                    }
                    break;
                case 'closed':
                    return $this->error('The classroom is closed!', 400);
            }
            DB::table('classes_students')->insert([
                'class_id' => $class->id,
                'student_id' => $student->id
            ]);
        } else if ($exists->is_unsubscribed) {
            DB::table('classes_students')
                ->where('class_id', $class_id)
                ->where('student_id', $student->id)
                ->update([
                    'is_unsubscribed' => false
                ]);
        }
        return $this->success([
            'item' => $class,
        ]);
    }

    public function unsubscribeClass($class_id) {
        $student = $this->user;
        DB::table('classes_students')
            ->where('class_id', $class_id)
            ->where('student_id', $student->id)
            ->update([
                'is_unsubscribed' => true
            ]);
        return $this->success('OK.');
    }

    public function changeOptions() {
        $student = $this->user;
        if (request()->has('is_test_timer_displayed')) {
            $student->is_test_timer_displayed = request('is_test_timer_displayed') ? true : false;
        }
        if (request()->has('is_test_questions_count_displayed')) {
            $student->is_test_questions_count_displayed = request('is_test_questions_count_displayed') ? true : false;
        }
        $student->save();
        return $this->success('OK.');
    }

    public function downloadAssignmentsReport(Request $request, $class_id, $format = 'csv') {
        $user_id = $this->user->id;
        $detailed_report = DB::table('class_detailed_reports')
            ->where('class_id', $class_id)
            ->where('student_id', $user_id)
            ->first();
        if (!$detailed_report) {
            return $this->error('Error.', 404);
        }
        $data = json_decode($detailed_report->data);
        $assignments = Application::where('type', 'assignment')
            ->whereHas('classes', function ($q) use ($class_id) {
                $q->where('classes.id', $class_id);
            })
            ->orderBy('name', 'ASC')
            ->get()->keyBy('id');
        foreach ($assignments as $app) {
            $class_data = $app->getClassRelatedData($class_id);
            if ($class_data->is_for_selected_students) {
                if (DB::table('classes_applications_students')
                        ->where('class_app_id', $class_data->id)
                        ->where('student_id', $this->user->id)->count() < 1) {
                    $assignments->forget($app->id);
                }
            }
        }
        switch ($format) {
            case 'xls':
                return (new StudentClassAssignmentsReportExport($assignments, $data))
                    ->download('assignments_report.xls', \Maatwebsite\Excel\Excel::XLS);
            case 'xlsx':
                return (new StudentClassAssignmentsReportExport($assignments, $data))
                    ->download('assignments_report.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            case 'tsv':
                return (new StudentClassAssignmentsReportExport($assignments, $data))
                    ->download('assignments_report.tsv', \Maatwebsite\Excel\Excel::TSV);
            case 'ods':
                return (new StudentClassAssignmentsReportExport($assignments, $data))
                    ->download('assignments_report.ods', \Maatwebsite\Excel\Excel::ODS);
            case 'html':
                return (new StudentClassAssignmentsReportExport($assignments, $data))
                    ->download('assignments_report.html', \Maatwebsite\Excel\Excel::HTML);
            /** PDF export require extra library: https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/#pdf
            case 'pdf':
            return (new StudentClassAssignmentsReportExport($assignments, $data))
            ->download('assignments_report.pdf', \Maatwebsite\Excel\Excel::MPDF/DOMPDF/TCPDF); */
            default:
                return (new StudentClassAssignmentsReportExport($assignments, $data))
                    ->download('assignments_report.csv', \Maatwebsite\Excel\Excel::CSV, [
                        'Content-Type' => 'text/csv',
                    ]);
        }
    }

    public function downloadTestsReport(Request $request, $class_id, $format = 'csv') {
        $user_id = $this->user->id;
        $max_attempts = 1;
        $tests = Application::where('type', 'test')
            ->whereHas('classes', function ($q) use ($class_id) {
                $q->where('classes.id', $class_id);
            })
            ->orderBy('name', 'ASC')
            ->get()->keyBy('id');
        foreach ($tests as $test) {
            $class_data = $test->getClassRelatedData($class_id);
            $class_app_id = $class_data->id;
            if ($class_data->is_for_selected_students && DB::table('classes_applications_students')
                    ->where('class_app_id', $class_app_id)->where('student_id', $user_id)->count() < 1) {
                $tests->forget($test->id);
                continue;
            }
            $test->attempts = StudentTestAttempt::whereHas('testStudent', function ($q1) use ($user_id, $class_app_id) {
                $q1->where('student_id', $user_id)->where('class_app_id', $class_app_id);
            })->get();
            $attempts_count = count($test->attempts);
            if ($attempts_count > $max_attempts) {
                $max_attempts = $attempts_count;
            }
        }
        switch ($format) {
            case 'xls':
                return (new StudentClassTestsReportExport($tests, $max_attempts))
                    ->download('tests_report.xls', \Maatwebsite\Excel\Excel::XLS);
            case 'xlsx':
                return (new StudentClassTestsReportExport($tests, $max_attempts))
                    ->download('tests_report.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            case 'tsv':
                return (new StudentClassTestsReportExport($tests, $max_attempts))
                    ->download('tests_report.tsv', \Maatwebsite\Excel\Excel::TSV);
            case 'ods':
                return (new StudentClassTestsReportExport($tests, $max_attempts))
                    ->download('tests_report.ods', \Maatwebsite\Excel\Excel::ODS);
            case 'html':
                return (new StudentClassTestsReportExport($tests, $max_attempts))
                    ->download('tests_report.html', \Maatwebsite\Excel\Excel::HTML);
            /** PDF export require extra library: https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/#pdf
            case 'pdf':
            return (new StudentClassTestsReportExport($tests, $max_attempts))
            ->download('tests_report.pdf', \Maatwebsite\Excel\Excel::MPDF/DOMPDF/TCPDF); */
            default:
                return (new StudentClassTestsReportExport($tests, $max_attempts))
                    ->download('tests_report.csv', \Maatwebsite\Excel\Excel::CSV, [
                        'Content-Type' => 'text/csv',
                    ]);
        }
    }
}
