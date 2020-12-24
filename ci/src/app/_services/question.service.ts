import { Injectable } from '@angular/core';

@Injectable()
export class QuestionService {

    public question: any = null;
    public answers: string[] = null;

    isCorrect(question: any, answers: string[]) {
        this.question = question;
        this.answers = answers;
        // sort question answers
        if (this.question.question_order) {
            this.question.answers.sort((a, b) => {
                return a.value - b.value;
            });
            // check if all answers are numbers
            let isNumbers = true;
            for (let i = 0; i < this.answers.length; i++) {
                const answer = this.answers[i].replace(',', '.');
                if (isNaN(+answer)) {
                    isNumbers = false;
                    break;
                }
            }
            if (isNumbers) {
                for (let i = 0; i < this.answers.length; i++) {
                    this.answers[i] = this.answers[i].replace(',', '.');
                }
                this.answers.sort((a, b) => {
                    return +a - +b;
                });
            } else {
                this.answers.sort();
            }
        }
        // convert percents to float for FB
        if (this.question.reply_mode === 'FB') {
            for (let i = 0; i < this.answers.length; i++) {
                try {
                    if ((this.answers[i] + '').includes('%')) {
                        const answer = this.answers[i].replace('%', '');
                        if (!isNaN(+answer)) {
                            this.answers[i] = parseFloat(answer) / 100 + '';
                        }
                    }
                } catch (err) {
                    return false;
                }
            }
        }
        // check if answer is correct
        if (this.question.answer_mode === 'order') {
            for (let i = 0; i < this.question.answers.length; i++) {
                if (this.question.answers[i].value !== this.answers[i]) {
                    return false;
                }
            }
            return true;
        } else if (this.question.answer_mode === 'radio') {
            if (this.answers[0] === '') {
                return false;
            }
            const answer = +this.answers[0];
            if (answer < 0 || answer >= this.question.answers.length) {
                return false;
            }
            if (this.question.answers[answer].is_correct) {
                return true;
            }
        } else {
            if (this.answers.length < this.question.answers.length) {
                return false;
            }
            if (this.question.reply_mode === 'FB') {
                let depended_answers = true;
                let xIndex = -1;
                for (let i = 0; i < this.question.answers.length; i++) {
                    if ((this.question.answers[i].value + '').includes('x')) {
                        if (this.question.answers[i].value === 'x') {
                            xIndex = i;
                        }
                    } else {
                        depended_answers = false;
                        break;
                    }
                }
                if (xIndex >= 0 && depended_answers) {
                    const Parser = require('expr-eval').Parser;
                    const parser = new Parser();
                    let xValue = null;
                    for (let i = 0; i < this.question.answers.length; i++) {
                        if (this.question.answers[i].value === 'x' && !xValue) {
                            xValue = this.answers[i];
                            break;
                        }
                    }
                    for (let i = 0; i < this.question.answers.length; i++) {
                        const expr = parser.parse(this.question.answers[i].value);
                        if (!(+expr.evaluate({x: xValue}) === +(this.answers[i].trim()))) {
                            return false;
                        }
                    }
                    return true;
                }
            }
            for (let i = 0; i < this.question.answers.length; i++) {
                let correctAnswer = this.question.answers[i].value;
                if (this.question.answer_mode === 'checkbox') {
                    if (this.question.answers[i].is_correct && this.answers[i] === ''
                        || !this.question.answers[i].is_correct && this.answers[i] !== '') {
                        return false;
                    }
                } else {
                    if (this.answers[i] === '') {
                        return false;
                    }
                    if (this.question.conversion && this.question.reply_mode !== 'TF') {
                        // convert users answer
                        this.answers[i] = this.answers[i].replace(/[^\d.-\/]/g, '');
                        let temp = this.answers[i].split('/');
                        if (temp[1] !== undefined) {
                            this.answers[i] = (Number(temp[0]) / Number(temp[1])) + '';
                        } else {
                            this.answers[i] = temp[0] + '';
                        }
                        // convert correct answer
                        if ((correctAnswer + '').includes('/')) {
                            correctAnswer = correctAnswer.replace(/[^\d.-\/]/g, '');
                            temp = correctAnswer.split('/');
                            if (temp[1] !== undefined) {
                                correctAnswer = (Number(temp[0])
                                    / Number(temp[1])) + '';
                            } else {
                                correctAnswer = temp[0] + '';
                            }
                        }
                        // round answers
                        if (this.question.answers_round > 0) {
                            correctAnswer = Math.round(
                                correctAnswer * Math.pow(10,
                                this.question.answers_round)) / Math.pow(10, this.question.answers_round);
                            this.answers[i] = Math.round(+this.answers[i] * Math.pow(10,
                                this.question.answers_round)) / Math.pow(10, this.question.answers_round) + '';
                        }
                    } else if (this.question.rounding && this.question.reply_mode !== 'TF') {
                        this.answers[i] = this.answers[i].replace(/[^\d.-]/g, '');
                        const temp = ('' + correctAnswer).split('.');
                        let roundTo = 0;
                        if (temp[1] !== undefined) {
                            roundTo = temp[1].length;
                        }
                        this.answers[i] = Number(this.answers[i]).toFixed(roundTo) + '';
                    }
                    if (this.question.answers[i].is_correct &&
                        // tslint:disable-next-line:triple-equals
                        correctAnswer != this.answers[i].trim()) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

}
