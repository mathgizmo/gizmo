<?php

namespace App\Http\AdminControllers;

use App\ClassOfStudents;
use App\Progress;
use App\StudentsTrackingQuestion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dashboard;

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
                                $complete_lessons_count = Progress::where('entity_type', 'lesson')->where('app_id', $app->id)
                                    ->where('student_id', $student->id)->count();
                                $lessons_count = 0;
                                if (!$is_completed && $complete_lessons_count > 0) {
                                    foreach ($app->getTopics() as $topic) {
                                        $topic_lessons_count = DB::table('lesson')->whereIn('id', function($q) use($app) {
                                            $q->select('model_id')->from('application_has_models')->where('model_type', 'lesson')
                                                ->where('app_id', $app->id);
                                        })->where('topic_id', $topic->id)->count();
                                        if ($topic_lessons_count) {
                                            $lessons_count += $topic_lessons_count;
                                        } else {
                                            $lessons_count += DB::table('lesson')->where('topic_id', $topic->id)->count();
                                        }
                                    }
                                }
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

    public function deleteOldAnswersStatistics() {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        $date = Carbon::now()->subYears(1)->toDateString();
        StudentsTrackingQuestion::where('created_at', '<', $date)->delete();
        return response()->json(['status' => 'success'], 200);
    }

}
