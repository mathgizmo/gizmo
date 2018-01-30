import { Component, OnInit, Inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { TopicService } from '../_services/index';
import { TrackingService } from '../_services/index';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
    moduleId: module.id,
    templateUrl: 'lesson.component.html',
    providers: [TopicService, TrackingService]
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
    private sub: any;

    constructor(
            private topicService: TopicService,
            private trackingService: TrackingService,
            private route: ActivatedRoute,
            public dialog: MatDialog
            ) { }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.topic_id = +params['topic_id']; // (+) converts string 'id' to a number
            this.lesson_id = +params['lesson_id']; // (+) converts string 'id' to a number

            // In a real app: dispatch action to load the details here.
            // get lesson tree from API
            this.topicService.getLesson(this.topic_id, this.lesson_id)
                .subscribe(lessonTree => {
                    this.lessonTree = lessonTree;
                    if (lessonTree['questions'].length) {
                        this.nextQuestion();
                        this.trackingService.startLesson(this.lesson_id).subscribe(start_time => {
                            this.start_time = start_time;
                        });
                    }
                });
         });
    }

    nextQuestion() {
        this.answers = [];
        this.question = this.lessonTree['questions'].shift();
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
            let dialogRef = this.dialog.open(GoodDialogComponent, {
                width: '250px',
                data: { }
            });

            dialogRef.afterClosed().subscribe(result => {
                if (this.lessonTree['questions'].length) {
                    this.nextQuestion();
                } else {
                    this.question = null;
                    this.trackingService.doneLesson(this.lesson_id, this.start_time, this.weak_questions).subscribe();
                }
            });
        } else {
            if (this.weak_questions.indexOf(this.question.id) === -1) {
                this.weak_questions.push(this.question.id);
            }
            this.lessonTree['questions'].push(this.question);
            let dialogRef = this.dialog.open(BadDialogComponent, {
                width: '250px',
                data: { data: this.question.answers.filter(function(answer){
                    if (answer.is_correct == 1) return true;
                    return false;
                    }) , explanation: this.question.explanation
                }
            });

            dialogRef.afterClosed().subscribe(result => {
                if (this.lessonTree['questions'].length) {
                    this.nextQuestion();
                } else {
                    this.question = null;
                    this.trackingService.doneLesson(this.lesson_id, this.start_time, this.weak_questions).subscribe();
                }
            });
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
                    if (this.question.answers[i].is_correct && this.question.answers[i].value != this.answers[i]) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }
}

@Component({
    selector: 'good-dialog',
    template: `<h2 mat-dialog-title>Correct!</h2>
        <mat-dialog-content></mat-dialog-content>
        <mat-dialog-actions>
          <button mat-button [mat-dialog-close]="true" style="background-color: yellow">Continue</button>
        </mat-dialog-actions>`
})
export class GoodDialogComponent {
    constructor(
        public dialogRef: MatDialogRef<GoodDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {

    }

    onNoClick(): void {
        this.dialogRef.close();
    }
}

@Component({
    selector: 'bad-dialog',
    template: `<h2 mat-dialog-title>Incorrect :(</h2>
        <mat-dialog-content>
            <div *ngIf="answers.length == 1">
                Correct answer is: {{answers[0].value}}
            </div>
            <div *ngIf="answers.length != 1">
                Correct answers are: <ul>
                <li *ngFor="let answer of answers; let answerIndex = index">{{answer.value}}</li>
                </ul>
            </div>
            <div *ngIf="explanation!=''">
                {{explanation}}
            </div>
        </mat-dialog-content>
        <mat-dialog-actions>
          <button mat-button [mat-dialog-close]="true" style="background-color: yellow">Continue</button>
        </mat-dialog-actions>`
})
export class BadDialogComponent {
    answers: string[];
    explanation: string;

    constructor(
        public dialogRef: MatDialogRef<BadDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        this.answers = data.data;
        this.explanation = data.explanation;
    }

    onNoClick(): void {
        this.dialogRef.close();
    }
}