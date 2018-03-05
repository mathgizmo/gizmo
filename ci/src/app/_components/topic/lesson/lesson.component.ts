import { Component, OnInit, OnDestroy, Inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { TopicService } from '../../../_services/index';
import { TrackingService } from '../../../_services/index';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA, 
  MatProgressBarModule } from '@angular/material';
import { Router } from '@angular/router';

@Component({
    moduleId: module.id,
    templateUrl: 'lesson.component.html',
    providers: [TopicService, TrackingService],
    styleUrls: [ './lesson.component.css']
})
export class LessonComponent implements OnInit, OnDestroy {
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
    chart: SafeHtml;
    chart_height: number = 250; // dimension of chart area in px
    private bubleChartRebuildFunctionId; // id of function which rebuild buble chart

    constructor(
            private router: Router,
            private topicService: TopicService,
            private trackingService: TrackingService,
            private route: ActivatedRoute,
            public dialog: MatDialog,
            private sanitizer: DomSanitizer
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

    ngOnDestroy() {
      this.destroyBubleChart();
    }

    nextQuestion() {
        this.answers = [];
        this.question = this.lessonTree['questions'].shift();

        this.is_chart = false;
        if(this.question['question'].indexOf('%%chart{') >= 0){
            this.buildChart();
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
      this.destroyBubleChart();
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

    // function to build charts
    /**
      - type 1 (A1): 
      %%chart{type:1; value:0.3}%% 

      - type 2 (A4):
      %%chart{type:2; value:0.3}%% 

      - type 3 (A5):
      %%chart{type:3; value:0.3; max: 10}%% 

      value: percent fill (0-1)
      max: max value
    */
    private buildChart() {
      let chart = this.question['question'].match(/[^{}]+(?=\}%%)/g);
      let chart_type = 
        +chart['0'].match(/type:([0-9]+)(?=;)/g)['0'].replace('type:', '');
      let chart_value, chart_max_value;
      if (chart['0'].indexOf('max:') >= 0) {
        chart_value = 
          +chart['0'].match(/value:(.*)(?=;)/g)['0'].replace('value:', '');
        chart_max_value = 
          +chart['0'].match(/max:(.*)/g)['0'].replace('max:', '');
      } else {
        chart_value = 
          +chart['0'].match(/value:(.*)/g)['0'].replace('value:', '');
      }
      let chartHtml = this.question['question']
        .replace(/%%chart(.*)(?=%)%/g, "");
      this.is_chart = true;
      switch (chart_type) {
        case 1:
          // Chart (type 1 - rectangle)
          chartHtml += '<svg style="height: '
            + this.chart_height + '; width:' + this.chart_height + ';">';
          chartHtml += '<rect id="rect2" style="height:'
            + this.chart_height +' !important; width: 100%;"></rect>'
          chartHtml += '<rect id="rect1" style="height:'+ 
            chart_value*this.chart_height +' !important; width: 100%;"></rect>';
          chartHtml += '</svg>';
          this.chart = this.sanitizer.bypassSecurityTrustHtml(chartHtml);
          break;
        case 2:
          // Chart (type 2 - circle)
          let radius = this.chart_height/2;
          let angle = 2*Math.PI*chart_value;
          let x = radius + radius*Math.sin(angle);
          let  y = radius - radius*Math.cos(angle);
          chartHtml += '<svg style="height: '
            + this.chart_height + '; width:' + this.chart_height + ';">';
          if(chart_value < 0.99999) {
            chartHtml += '<circle id="circle2" style="r: ' + radius 
              + ' !important; cx: '+ radius + ' !important; cy: ' 
              + radius + ' !important;"/>';
            chartHtml += '<path id="circle1" d="M'+ radius +','+ radius 
              + ' L' + radius + ',0 A' + radius + ',' + radius;
            if(chart_value <= 0.5){
              chartHtml += ' 1 0,1';
            } else {
              chartHtml += ' 1 1,1';  
            }
            chartHtml += ' ' + x + ', ' + y +' z"></path>'; 
          } else {
            chartHtml += '<circle id="circle1" style="r: ' + radius 
              + ' !important; cx: '+ radius + ' !important; cy: ' 
              + radius + ' !important;"/>';
          }
          chartHtml += '</svg>';
          this.chart = this.sanitizer.bypassSecurityTrustHtml(chartHtml);
          break;
        case 3:
          // Chart (type 3 - points)
          document.getElementById('chart-container').innerHTML = chartHtml;
          let canvas = document.createElement("canvas");
          document.getElementById('chart-container').appendChild(canvas);
          canvas.style.height = '100%';
          canvas.style.width = '100%';
          let ctx = canvas.getContext("2d"); 
          let bubbles = [];
          let bubleRadius = 4;
          for(let i = 0; i < chart_max_value; i++) {
            bubbles[i] = {
              x: Math.random() * (canvas.width-bubleRadius*2),
              y: Math.random() * (canvas.height-bubleRadius*2),
              radius: bubleRadius
            };
          }
          this.bubleChartRebuildFunctionId = setInterval(() => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            this.drawBublesChart(chart_value, chart_max_value, ctx, canvas, bubbles);
          }, 200);
          break;
        default:
          break;
      }
    }

    // function to draw Bubles Chart
    private drawBublesChart(bublesPercent: number, 
      maxBublesNum: number, ctx: CanvasRenderingContext2D,
      canvas: HTMLCanvasElement, bubles) {
      let bublesNum = Math.round(maxBublesNum*bublesPercent);
      for (let i = 0; i < bublesNum; i++) {
        bubles[i] = this.drawBuble(2, ctx, canvas, bubles[i]);
      }
      for (let i = bublesNum; i < maxBublesNum; i++) {
        bubles[i] = this.drawBuble(1, ctx, canvas, bubles[i]);
      }
    }

    // function to draw one Buble
    private drawBuble(type: number, ctx: CanvasRenderingContext2D,
      canvas:HTMLCanvasElement, buble) {
      if(type == 1) {
        ctx.fillStyle = "#111";
      }
      if(type == 2) {
        ctx.fillStyle = "#66cccc";
      }
      buble.x += Math.random() * 2 - 1;
      buble.y += Math.random() * 2 - 1;

      // Check if buble goes beyond the field
      let bubleDiameter = buble.radius*2;
      if(buble.x > canvas.width-bubleDiameter)
        buble.x = canvas.width-bubleDiameter;
      if(buble.x < bubleDiameter)
        buble.x = bubleDiameter;
      if(buble.y > canvas.height-bubleDiameter)
        buble.y = canvas.height-bubleDiameter;
      if(buble.y < bubleDiameter)
        buble.y = bubleDiameter;

      ctx.beginPath();
      ctx.arc(buble.x, buble.y, buble.radius, 0, Math.PI*2, true);
      ctx.fill();
      return buble;
    }

    // remove buble chart rebuild function if it exists
    private destroyBubleChart() {
      if (this.bubleChartRebuildFunctionId)
        clearInterval(this.bubleChartRebuildFunctionId); 
    }

}

@Component({
    selector: 'good-dialog',
    template: `<h2 mat-dialog-title>Correct!</h2>
        <mat-dialog-content></mat-dialog-content>
        <mat-dialog-actions>
          <button mat-button [mat-dialog-close]="true" style="background-color: #fef65b">Continue</button>
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
            <div *ngIf="(answers.length == 1) && showAnswer">
                Correct answer is: {{answers[0].value}}
            </div>
            <div *ngIf="(answers.length != 1) && showAnswer">
                Correct answers are: 
                <ul>
                    <li *ngFor="let answer of answers; let answerIndex = index">
                        {{answer.value}}
                    </li>
                </ul>
            </div>
            <div *ngIf="explanation!=''">
                {{explanation}}
            </div>
        </mat-dialog-content>
        <mat-dialog-actions>
            <button mat-button [mat-dialog-close]="false" style="background-color: #fef65b">Continue</button>
            <button mat-button [mat-dialog-close]="true" style="background-color: #ff4444">Report Error!</button>
        </mat-dialog-actions>`,
    styles: [`
        div { min-height: 40px; }
    `]
})
export class BadDialogComponent {
    answers: string[];
    explanation: string;
    showAnswer: boolean;

    constructor(
        public dialogRef: MatDialogRef<BadDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        this.answers = data.data;
        this.explanation = data.explanation;
        this.showAnswer = data.showAnswers;
        //this.answers[0]['value'] = "$$E=mc^2$$"; // test equation in LaTeX
        setTimeout(function() {
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        }, 50);
    }

    onNoClick(): void {
        this.dialogRef.close();
    }
}

@Component({
    selector: 'report-dialog',
    template: `<h2 mat-dialog-title>Please specify reason</h2>
        <mat-dialog-content>
            <mat-radio-group class="radio-group" [(ngModel)]="selectedOption">
              <mat-radio-button class="radio-button" *ngFor="let option of options; let optionIndex = index" [value]="optionIndex">
                {{option}}
              </mat-radio-button>
            </mat-radio-group>
        </mat-dialog-content>
        <mat-form-field *ngIf="selectedOption == 3">
            <input matInput [(ngModel)]="custom">
        </mat-form-field>
        <mat-dialog-actions>
            <button mat-button [mat-dialog-close]="{option: options[selectedOption], text: custom, question_id: question_id, answers: answers}" style="background-color: #31698a">Send</button>
            <button mat-button [mat-dialog-close]="false" style="background-color: #6dc066">Cancel</button>
        </mat-dialog-actions>`
})
export class ReportDialogComponent {
    custom: string;
    selectedOption: number;
    answers: string[];
    question_id: number;

    options = [
        'Wording of question is confusing or unclear',
        'Answer is incorrect',
        'question does not belong in this topic',
        'other',
      ];

    constructor(
        public dialogRef: MatDialogRef<ReportDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        this.custom = "";
        this.answers = data.answers;
        this.question_id = data.question_id;
    }

    onNoClick(): void {
        this.dialogRef.close();
    }
}