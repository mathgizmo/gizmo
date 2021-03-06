<?php

namespace App\Http\AdminControllers;

use App\ClassApplication;
use App\ClassApplicationStudent;
use App\ClassOfStudents;
use App\Mail\ClassMail;
use App\Mail\TestErrorMail;
use App\Progress;
use App\StudentsTrackingQuestion;
use App\StudentTestAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class JobController extends Controller
{
    public function generateClassDetailedReports(Request $request) {
        $error = null;
        try {
            $now = Carbon::now()->toDateTimeString();
            if ($request['class_id']) {
                $classes = ClassOfStudents::where('id', $request['class_id'])->get();
            } else {
                $classes = ClassOfStudents::all();
            }
            foreach ($classes as $class) {
                try {
                    $students = $class->students()->orderBy('email')
                        ->get(['students.id', 'students.first_name', 'students.last_name', 'students.email']);
                    $apps = $class->assignments()->get();
                    foreach ($students as $student) {
                        $data = [];
                        foreach ($apps as $app) {
                            try {
                                $class_data = $app->getClassRelatedData($class->id);
                                if ($class_data->is_for_selected_students) {
                                    if (DB::table('classes_applications_students')->where('class_app_id', $class_data->id)
                                        ->where('student_id', $student->id)->count() < 1) {
                                        continue;
                                    }
                                }
                                if ($class_data->due_date) {
                                    $due_at = $class_data->due_time ? $class_data->due_date.' '.$class_data->due_time : $class_data->due_date.' 00:00:00';
                                } else {
                                    $due_at = null;
                                }
                                $completed_at = $app->getCompletedDate($student->id);
                                $now = Carbon::now()->toDateTimeString();
                                $is_completed = Progress::where('entity_type', 'application')->where('entity_id', $app->id)
                                        ->where('student_id', $student->id)->count() > 0;
                                $is_past_due = (!$is_completed && $due_at && $due_at < $now) ||
                                    ($is_completed && $due_at && $completed_at && $due_at < $completed_at);
                                $app_lessons = $app->getLessonsQuery()->pluck('id');
                                $complete_lessons_count = Progress::where('entity_type', 'lesson')
                                    ->whereIn('entity_id', $app_lessons)
                                    ->where('app_id', $app->id)
                                    ->where('student_id', $student->id)
                                    ->count();
                                $lessons_count = $app_lessons->count() ?: 0;
                                $status = 'pending';
                                $progress = 0;
                                if ($is_completed) {
                                    $status = 'completed';
                                    $progress = 1;
                                } else if ($is_past_due) {
                                    $status = 'overdue';
                                    $progress = $lessons_count > 0 ? (round($complete_lessons_count / $lessons_count, 3)) : 0;
                                } else if ($complete_lessons_count > 0) {
                                    $status = 'progress';
                                    $progress = $lessons_count > 0 ? (round($complete_lessons_count / $lessons_count, 3)) : 0;
                                }
                                $data[$app->id] = (object) [
                                    'app_id' => $app->id,
                                    'status' => $status,
                                    'progress' => $progress
                                ];
                            } catch (\Exception $e) {
                                $error = $e->getMessage();
                            }
                        }
                        if (DB::table('class_detailed_reports')
                                ->where('class_id', $class->id)
                                ->where('student_id', $student->id)
                                ->count() > 0) {
                            DB::table('class_detailed_reports')
                                ->where('class_id', $class->id)
                                ->where('student_id', $student->id)
                                ->update([
                                    'student_email' => $student->email,
                                    'data' => json_encode($data),
                                    'updated_at' => $now
                                ]);
                        } else {
                            DB::table('class_detailed_reports')->insert([
                                'class_id' => $class->id,
                                'student_id' => $student->id,
                                'student_email' => $student->email,
                                'data' => json_encode($data),
                                'created_at' => $now,
                                'updated_at' => $now
                            ]);
                        }
                    }
                    DB::table('class_detailed_reports')
                        ->where('class_id', $class->id)
                        ->whereNotIn('student_id', $students->pluck('id')->toArray())
                        ->delete();
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        if ($error) {
            return response()->json(['status' => 'error', 'message' => $error], 500);
        } else {
            return response()->json(['status' => 'success'], 200);
        }
    }

    public function checkTestsTimeout() {
        $error = false;
        $attempts = [];
        $now = \Illuminate\Support\Carbon::now()->toDateTimeString();
        foreach (StudentTestAttempt::whereNull('end_at')->get() as $attempt) {
            try {
                $test_student = ClassApplicationStudent::where('id', $attempt->test_student_id)->first();
                if (!$test_student) { continue; }
                $test = ClassApplication::where('id', $test_student->class_app_id)->first();
                if ($test && ($test->duration || $test->due_date)) {
                    $is_ended = false;
                    if ($test->due_date) {
                        $due_at = $test->due_time ? $test->due_date.' '.$test->due_time : $test->due_date.' 00:00:00';
                        $is_ended = $now > $due_at;
                    }
                    if ($test->duration) {
                        if ($attempt->isCorrupted()) {
                            $attempt->is_error = true;
                            $attempt->save();
                            $is_ended = true;
                        } else {
                            $class_student = DB::table('classes_students')
                                ->where('class_id', $test->class_id)
                                ->where('student_id', $test_student->student_id)
                                ->first();
                            $duration = $class_student
                                ? ($test->duration * $class_student->test_duration_multiply_by)
                                : $test->duration;
                            $time_left = $duration - Carbon::now()->diffInSeconds(Carbon::parse($attempt->start_at));
                            if ($time_left < 1) {
                                $is_ended = true;
                            }
                        }
                    }
                    if ($is_ended) {
                        $answers_statistics = DB::table('students_test_questions')
                            ->select(
                                DB::raw("SUM(1) as total"),
                                DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
                            )
                            ->where('attempt_id', $attempt->id)
                            ->first();
                        $attempt->mark = $answers_statistics->total > 0
                            ? $answers_statistics->complete / $answers_statistics->total : 1;
                        $attempt->end_at = $now;
                        $attempt->save();
                        array_push($attempts, $attempt->id);
                    }
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        if (config('app.env') == 'production') {
            foreach (StudentTestAttempt::where('is_error', true)->whereNull('error_emailed_at')->get() as $attempt) {
                try {
                    $test_student = $attempt->testStudent;
                    $test = $test_student ? $test_student->classApplication : null;
                    $class = $test ? $test->classOfStudents : null;
                    $teacher = $class ? $class->teacher : null;
                    $student = $test_student->student;
                    if ($teacher && $student) {
                        Mail::to($teacher->email)->send(new TestErrorMail($student, $class, $test));
                        $attempt->error_emailed_at = Carbon::now()->toDateTimeString();
                        $attempt->save();
                    }
                } catch (\Exception $e) { return $e;}
            }
        }
        if ($error) {
            return response()->json(['status' => 'error', 'message' => $error], 500);
        } else {
            return response()->json(['status' => 'success', 'attempts' => $attempts], 200);
        }
    }

    public function deleteOldAnswersStatistics() {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        $date = Carbon::now()->subYears(1)->toDateString();
        StudentsTrackingQuestion::where('created_at', '<', $date)->delete();
        return response()->json(['status' => 'success'], 200);
    }

}
