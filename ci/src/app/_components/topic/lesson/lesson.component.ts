import { Component, OnInit, Inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { TopicService } from '../../../_services/index';
import { TrackingService } from '../../../_services/index';
import { MatDialog, MatProgressBarModule } from '@angular/material';
import { Router } from '@angular/router';

import { GoodDialogComponent } from './good-dialog/good-dialog.component';
import { BadDialogComponent} from './bad-dialog/bad-dialog.component';
import { ReportDialogComponent } from './report-dialog/report-dialog.component';

@Component({
    moduleId: module.id,
    templateUrl: 'lesson.component.html',
    providers: [TopicService, TrackingService],
    styleUrls: [ './lesson.component.css']
})
export class LessonComponent implements OnInit {
    lessonTree: any = [];
    topic_id: number;
    lesson_id: number;
    question: any = null;
    answer: string = '';
    answers: string[];
    weak_questions: string[] = [];
    start_time: any = '';
    initial_loading = 1;
    next = 0;
    private sub: any;

    question_num : number;
    correct_answers : number;
    complete_percent : number;

    incorrect_answers: number;
    max_incorrect_answers: number = 1;

    is_chart: boolean;

    constructor(
            private router: Router,
            private topicService: TopicService,
            private trackingService: TrackingService,
            private route: ActivatedRoute,
            public dialog: MatDialog
            ) { 

        if (localStorage.getItem('question_num') != undefined) {
            this.question_num = Number(localStorage.getItem('question_num'));
        }
        else {
            this.question_num = 4;
        }
        this.is_chart = false;
    }

    ngOnInit() {
        this.question_num = +localStorage.getItem('question_num');
        this.incorrect_answers = 0;
        this.sub = this.route.params.subscribe(params => {
            this.topic_id = +params['topic_id']; // (+) converts string 'id' to a number
            this.lesson_id = (params['lesson_id'] == "testout") ? -1 :
                +params['lesson_id']; // (+) converts string 'id' to a number
            if (this.lesson_id == -1) {
                this.question_num = 0;
            }

            // get lesson tree from API
            this.topicService.getLesson(this.topic_id, this.lesson_id)
                .subscribe(lessonTree => {
                    this.lessonTree = lessonTree;
                    this.initial_loading = 0;
                    if (lessonTree['questions'].length) {
                        if(this.question_num >= this.lessonTree['questions'].length)
                            this.question_num = this.lessonTree['questions'].length;
                        this.nextQuestion();
                        this.trackingService.startLesson(this.lesson_id).subscribe(start_time => {
                            this.start_time = start_time;
                        });
                    }
                    if (this.lesson_id == -1) {
                        this.next = lessonTree['next_topic_id'];
                    }
                    else {
                        this.next = lessonTree['next_lesson_id'];
                    }
                    this.correct_answers = this.complete_percent = 0;
                });
         });
    }

    nextQuestion() {
        this.answers = [];
        this.question = this.lessonTree['questions'].shift();

        this.is_chart = false;
        if(this.question['question'].indexOf('%%chart{') >= 0){
            this.is_chart = true;
        }

        if (['mcqms'].indexOf(this.question.reply_mode) >= 0) {
            for (var i = 0; i < this.question.answers.length; i++) {
                this.answers.push('');
            }
            this.question.answer_mode = 'checkbox';
        } else if (['mcq'].indexOf(this.question.reply_mode) >= 0) {
            this.answers.push('');
            this.question.answer_mode = 'radio';
        } else if (['TF'].indexOf(this.question.reply_mode) >= 0) {
            this.answers.push('');
            this.question.answer_mode = 'TF';
        } else if (['order'].indexOf(this.question.reply_mode) >= 0) {
            for (var i = 0; i < this.question.answers.length; i++) {
                this.answers.push(this.question.answers[i].value);
            }
            this.answers = this.shuffle(this.answers);
            this.question.answer_mode = 'order';
        } else {
            for (var i = 0; i < this.question.answers.length; i++) {
                this.answers.push('');
            }
            this.question.answer_mode = 'input';
        }
        setTimeout(function() {
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        }, 50);
    }
    
    checkAnswer() {
      if (this.isCorrect()) {
        this.correct_answers++;
        this.complete_percent = (this.correct_answers == 0) ? 0
            : this.correct_answers/this.question_num*100;
        //if we have enough correct responces just remove rest of the questions
        if(this.correct_answers == this.question_num
                && this.question_num != 0) {
           this.lessonTree['questions'] = [];
        }
        let dialogRef = this.dialog.open(GoodDialogComponent, {
            width: '300px',
            data: { }
        });

        dialogRef.afterClosed().subscribe(result => {
            if (this.lessonTree['questions'].length) {
                this.nextQuestion();
            } else {
                this.question = null;
                this.trackingService.doneLesson(this.topic_id, 
                  this.lesson_id, this.start_time, this.weak_questions).subscribe();
            }
        });
      } else {
          if (this.weak_questions.indexOf(this.question.id) === -1) {
              this.weak_questions.push(this.question.id);
          }
          this.incorrect_answers++;
          if(this.lesson_id == -1 && 
              this.incorrect_answers > this.max_incorrect_answers) {
                this.router.navigate(['/topic/'+this.topic_id]);
          } else {
              this.lessonTree['questions'].push(this.question);
          }
          let dialogRef = this.dialog.open(BadDialogComponent, {
              width: '300px',
              data: { data: this.question.answers.filter(function(answer){
                  if (answer.is_correct == 1) return true;
                  return false;
                  }) , explanation: this.question.explanation,
                  showAnswers: (this.lesson_id == -1) ? false : true
              }
          });

          dialogRef.afterClosed().subscribe(result => {
              if (result) {
                  let reportDialogRef = this.dialog.open(ReportDialogComponent, {
                      //width: '300px',
                      data: {question_id: this.question.id, answers: this.answers}
                  });
                  
                  reportDialogRef.afterClosed().subscribe(result => {
                      console.log(result);
                      this.topicService.reportError(result.question_id, result.answers, result.option, result.text).subscribe();
                  });
              }
              if (this.lessonTree['questions'].length) {
                  this.nextQuestion();
              } else {
                  this.question = null;
                  this.trackingService.doneLesson(this.topic_id, this.lesson_id, this.start_time, this.weak_questions).subscribe();
              }
          });
          if(this.lesson_id == -1) {
            this.question_num--;
          } else {
            this.correct_answers = this.complete_percent = 0;
          }
      }
    }
    
    isCorrect() {
        if (this.question.answer_mode == 'radio') {
            if (this.answers[0] === "") return false;
            let answer = +this.answers[0];
            if (answer < 0 || answer >= this.question.answers.length) return false;
            if (this.question.answers[answer].is_correct) {
                return true;
            }
        } else {
            if (this.answers.length < this.question.answers.length) {
                return false;
            }
            for (var i = 0; i < this.question.answers.length; i++) {
                if (this.question.answer_mode == 'checkbox') {
                    if (this.question.answers[i].is_correct && this.answers[i] === ""
                        || !this.question.answers[i].is_correct && this.answers[i] !== "") {
                        return false;
                    }
                } else {
                    if (this.answers[i] === "") return false;
                    if (this.question.conversion) {
                        this.answers[i] = this.answers[i].replace(/[^\d.-\/]/g,'');
                        let temp = this.answers[i].split("/");
                        if (temp[1] != undefined) {
                            this.answers[i] = (Number(temp[0])/Number(temp[1])) + "";
                        }
                        else {
                            this.answers[i] = temp[0] + "";
                        }
                    }
                    if (this.question.rounding) {
                        this.answers[i] = this.answers[i].replace(/[^\d.-]/g,'');
                        let temp = this.question.answers[i].value.split(".");
                        var roundTo = 0;
                        if (temp[1] != undefined) {
                            roundTo = temp[1].length;
                        }
                        this.answers[i] = Number(this.answers[i]).toFixed(roundTo) + "";
                    }
                    if (this.question.answers[i].is_correct && this.question.answers[i].value != this.answers[i]) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

    // function to shuffle answers in order
    private shuffle(array) {
      let currentIndex = array.length, temporaryValue, randomIndex;    
      // While there remain elements to shuffle...
      while (0 !== currentIndex) {    
        // Pick a remaining element...
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;    
        // And swap it with the current element.
        temporaryValue = array[currentIndex];
        array[currentIndex] = array[randomIndex];
        array[randomIndex] = temporaryValue;
      }    
      return array;
    }

}