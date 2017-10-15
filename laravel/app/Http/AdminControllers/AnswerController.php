<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Question;
use App\Answer;
use App\Http\Requests;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
		 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
		
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
			
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
		
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
    
    }


    /** 
     * Insert answer from question table
     *
     * @param  type text
     * @return  null
     */
    public function insertAnswerFromQuestions() {

        $questions = Question::where('type', 'text')->get();
     
        foreach ($questions as $key => $q) {

            switch ($q->reply_mode) {
                case 'general': //General
                    $this->createGeneralAnswer($q->reply_mode, $q);

                    break;

                case 'TF': //True or False
                    $this->createGeneralAnswer($q->reply_mode, $q);

                    break;

                case 'FB': //Fill In The Blank
                    $this->createFillAndBlankAnswer($q->reply_mode, $q);

                    break;

                case 'mcq3': //Multiple Choice 3
                case 'mcq4': //Multiple Choice 4
                case 'mcq5': //Multiple Choice 5
                case 'mcq6': //Multiple Choice 6
                    $this->createMultiChoiceAnswer($q->reply_mode, $q);
                    break;

                case 'ascending': //Ascending Order
                    $this->createFillAndBlankAnswer($q->reply_mode, $q);

                    break;

                case 'descending': //Descending Order
                    $this->createFillAndBlankAnswer($q->reply_mode, $q);

                    break;

                    
                
                default:
                    $this->createGeneralAnswer($q->reply_mode, $q);

                    break;
            }
            
        }
    }

    /** 
     * create genral answer / True false
     * @param  [type] $mode [description]
     * @return [type]       [description]
     */
    private function createGeneralAnswer($mode, $q) {

        for ($i=1; $i < $q->size; $i++) { 

            $answer_field = ($i > 1) ? 'answer'. $i : 'answer';

            $answer = new Answer;
            $answer->question_id = $q->id;
            $answer->value = $q->$answer_field;
            $answer->is_correct = 1;
            $answer->answer_order = 0;
            $answer->save();
        }
        
    }

    /** 
     * Create Fill in the Blank or Asc or Desc
     * @param  [type] $mode [description]
     * @return [type]       [description]
     */
    private function createFillAndBlankAnswer($mode, $q) {

        for ($i=1; $i < $q->size; $i++) { 

            $answer_field = ($i > 1) ? 'answer'. $i : 'answer';

            $answer = new Answer;
            $answer->question_id = $q->id;
            $answer->value = $q->$answer_field;
            $answer->is_correct = 1;
            $answer->answer_order = $i;
            $answer->save();
        }
    }

    /** 
     * Create Multiple Choices
     * @param  [type] $mode [description]
     * @return [type]       [description]
     */
    private function createMultiChoiceAnswer($mode, $q) {

        $ans_array = array();
        
        for ($i=1; $i < $q->size; $i++) { 

            $answer_field = ($i > 1) ? 'answer'. $i : 'answer';
            array_push($ans_array, $q->$answer_field);
        }

        for ($i=1; $i < $q->size; $i++) { 

            $is_correct = 0;
            $answer_field = ($i > 1) ? 'answer'. $i : 'answer';
            
            if( in_array($q->$answer_field, $ans_array) ) {
                $is_correct = 1;
            }

            $answer = new Answer;
            $answer->question_id = $q->id;
            $answer->value = $q->$answer_field;
            $answer->is_correct = $is_correct;
            $answer->answer_order = 0;
            $answer->save();
        }
    }
}

