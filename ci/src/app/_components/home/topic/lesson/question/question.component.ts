import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { MatProgressBarModule } from '@angular/material';

@Component({
  selector: 'app-question',
  templateUrl: './question.component.html',
  styleUrls: ['./question.component.scss']
})
export class QuestionComponent implements OnInit {

  private _question: any = null;
  @Input() set question(value: any) {
    this._question = value;
    this.ngOnInit();
  }
  get question(): any {
    return this._question;
  }

  @Output() onAnswered = new EventEmitter<string[]>();
  answers: string[];
  questionForChart: string = '';
  is_chart: boolean;

  constructor() { 
  	this.is_chart = false;
  }

  ngOnInit() {
  	this.answers = [];
    this.is_chart = false;
    if(this.question['question'].indexOf('%%chart{') >= 0){
        this.is_chart = true;
        this.questionForChart = this.question['question']
          .replace(new RegExp(/%%chart(.*)(?=%)%/g), "");
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
  	this.onAnswered.emit(this.answers);
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
