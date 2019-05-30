import {Component, OnInit, OnDestroy, Input, Output, EventEmitter} from '@angular/core';
import {MatProgressBarModule} from '@angular/material';

@Component({
    selector: 'app-question',
    templateUrl: './question.component.html',
    styleUrls: ['./question.component.scss']
})
export class QuestionComponent implements OnInit, OnDestroy {

    private _question: any = null;
    @Input() set question(value: any) {
        this._question = value;
        this.initQuestion();
    }

    get question(): any {
        return this._question;
    }

    @Output() onAnswered = new EventEmitter<string[]>();
    answers: string[] = [];
    questionForChart = '';
    is_chart = false;
    warning = false;
    warningMessage = 'Undefined exception';

    constructor() {
    }

    ngOnInit() {
    }

    ngOnDestroy() {
        document.removeEventListener('keyup', this.keyClick);
    }

    keyClick(event) {
        if (event.key === 'Enter') {
            this.checkAnswer();
        }
    }

    initQuestion() {
        this.keyClick = this.keyClick.bind(this);
        document.addEventListener('keyup', this.keyClick);
        this.answers = [];
        this.is_chart = false;
        if (this.question['question'].indexOf('%%chart{') >= 0) {
            this.is_chart = true;
            this.questionForChart = this.question['question']
                .replace(new RegExp(/%%chart(.*)(?=%)%/g), '');
        }
        if (['mcqms'].indexOf(this.question.reply_mode) >= 0) {
            for (let i = 0; i < this.question.answers.length; i++) {
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
            for (let i = 0; i < this.question.answers.length; i++) {
                this.answers.push(this.question.answers[i].value);
            }
            this.answers = this.shuffle(this.answers);
            this.question.answer_mode = 'order';
        } else {
            for (let i = 0; i < this.question.answers.length; i++) {
                this.answers.push('');
            }
            this.question.answer_mode = 'input';
            setTimeout(() => {
                $('.input:first').focus();
            }, 10);
        }
        setTimeout(function () {
            MathJax.Hub.Queue(['Typeset', MathJax.Hub]);
        }, 10);
    }

    checkAnswer() {
        if (this.answers.length < 1 || this.answers.every(elem => elem === '') ||
            (this.question.answer_mode === 'input' && this.answers.some(elem => elem === ''))) {
            this.warning = true;
            this.warningMessage = 'Please, answer the question!';
        } else {
            this.warning = false;
            document.removeEventListener('keyup', this.keyClick);
            this.onAnswered.emit(this.answers);
        }
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
