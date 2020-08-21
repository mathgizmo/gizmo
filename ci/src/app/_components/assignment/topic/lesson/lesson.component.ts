import {Component, OnInit, ViewChildren, QueryList, AfterViewChecked } from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {TopicService, TrackingService} from '../../../../_services';
import {MatDialog} from '@angular/material/dialog';
import {Router} from '@angular/router';
import {DeviceDetectorService} from 'ngx-device-detector';
import {GoodDialogComponent, BadDialogComponent, ReportDialogComponent, FeedbackDialogComponent, BadChallengeDialogComponent } from '../../../dialogs/index';

import { QuestionComponent } from './question/question.component';

@Component({
    moduleId: module.id,
    templateUrl: 'lesson.component.html',
    providers: [TopicService, TrackingService],
    styleUrls: ['./lesson.component.scss']
})
export class LessonComponent implements OnInit, AfterViewChecked {
    lessonTree: any = [];
    topic_id: number;
    next_topic_id: number;
    lesson_id: number;
    isChallenge: false;

    @ViewChildren(QuestionComponent)
    private questionComponents: QueryList<QuestionComponent>;

    weak_questions: string[] = [];
    start_time: any = '';
    initial_loading = 1;
    next = 0;
    unfinishedLessonsCount = 0;
    isUnfinished = false;
    private sub: any;

    question_num: number;
    correct_answers: number;
    complete_percent: number;

    incorrect_answers: number;
    randomisation = true;

    question: any = null;
    answers: string[] = null;

    all_questions: any = [];
    current_question_index = 0;

    dialogPosition: any;

    backLinkText = 'Back';
    titleText = 'Lesson';

    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    // test out variables
    private lessons_count: number;
    private current_question_order_no: number;
    public confident_value: number;
    public next_title: string;
    public testout_completed = false;
    public first_lesson_id = 1;
    public testout_empty = false;

    // public warning = false;
    // public warningMessage = 'Undefined exception';

    public ignoreAnswer = false; // ignore answer because user already answered this question wrong
    public fromContentReview = false;

    constructor(
        private router: Router,
        private topicService: TopicService,
        private trackingService: TrackingService,
        private route: ActivatedRoute,
        public dialog: MatDialog,
        private deviceService: DeviceDetectorService
    ) {
        if (localStorage.getItem('question_num') !== undefined) {
            this.question_num = Number(localStorage.getItem('question_num'));
        } else {
            this.question_num = 4;
        }
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngAfterViewChecked(): void {
        $('#testout-confident-level > *').detach().prependTo('#question-confident-level');
    }

    ngOnInit() {
        if (this.route.snapshot.queryParams['from_content_review']) {
            this.fromContentReview = true;
        }
        this.question_num = +localStorage.getItem('question_num');
        this.incorrect_answers = 0;
        this.sub = this.route.params.subscribe(params => {
            this.topic_id = +params['topic_id']; // (+) converts string 'id' to a number
            this.lesson_id = (params['lesson_id'] === 'testout') ? -1 :
                +params['lesson_id']; // (+) converts string 'id' to a number
            // get lesson tree from API
            this.topicService.getLesson(this.topic_id, this.lesson_id, this.fromContentReview)
                .subscribe(lessonTree => {
                    if (this.lesson_id === -1) {
                        if (lessonTree && lessonTree.questions && lessonTree.questions.length < 1) {
                            this.testout_empty = true;
                            this.titleText = this.titleText = 'Test Out Topic: ' + lessonTree.title;
                        }
                        this.lessons_count = +lessonTree['lessons_count'];
                        this.current_question_order_no = Math.round(this.lessons_count / 2);
                        this.first_lesson_id = +lessonTree['first_lesson_id'];
                    }
                    this.isChallenge = lessonTree.challenge;
                    this.all_questions.splice(0, this.all_questions.length, ...lessonTree['questions']);
                    this.initial_loading = 0;
                    if (lessonTree && lessonTree['questions'].length) {
                        // this.backLinkText = lessonTree.level + ' > ' + lessonTree.unit;
                        (this.lesson_id !== -1) ? this.titleText = lessonTree.topic.title + ': '
                            + lessonTree.title : this.titleText = 'Test Out Topic: ' + lessonTree.title;
                        this.randomisation = lessonTree['randomisation'];
                        if (this.randomisation) {
                            // randomize array
                            let currentIndex = lessonTree['questions'].length,
                                temporaryValue, randomIndex;
                            // While there remain elements to shuffle...
                            while (0 !== currentIndex) {
                                // Pick a remaining element...
                                randomIndex = Math.floor(Math.random() * currentIndex);
                                currentIndex -= 1;
                                // And swap it with the current element.
                                temporaryValue = lessonTree['questions'][currentIndex];
                                lessonTree['questions'][currentIndex] = lessonTree['questions'][randomIndex];
                                lessonTree['questions'][randomIndex] = temporaryValue;
                            }
                        }
                        this.lessonTree = lessonTree;
                        if (this.question_num >= this.lessonTree['questions'].length) {
                            this.question_num = this.lessonTree['questions'].length;
                        }
                        this.nextQuestion();
                        if (this.lesson_id !== -1) {
                            this.trackingService.startLesson(this.lesson_id, this.fromContentReview)
                                .subscribe(start_time => {
                                    this.start_time = start_time;
                                });
                        }
                        if (this.lesson_id === -1) {
                            this.question_num = lessonTree['max_questions_num']; // lessonTree['questions'].length;
                        }
                    }
                    if (this.lesson_id === -1) {
                        this.next = lessonTree['next_topic_id'];
                    } else {
                        this.next = lessonTree['next_lesson_id'];
                        this.next_topic_id = lessonTree['next_topic_id'];
                        if (lessonTree['unfinished_lessons_count']) {
                            this.unfinishedLessonsCount = lessonTree['unfinished_lessons_count'];
                            this.isUnfinished = lessonTree['is_unfinished'];
                        }
                    }
                    this.correct_answers = this.complete_percent = 0;
                });
        });
    }

    confidentChanged() {
        if (isNaN(+this.confident_value) || this.confident_value === null) {
            // this.warning = true;
            // this.warningMessage = 'Please, select your confident level for this question!';
            $('#continue-button').prop('disabled', true);
        } else {
            // this.warning = false;
            $('#continue-button').prop('disabled', false);
        }
    }

    nextQuestion() {
        if (this.lesson_id === -1) {
            if (this.question_num <= (this.correct_answers + this.incorrect_answers)) {
                this.next = this.question.lesson_id;
                this.next_title = this.question.lesson_title;
                this.question = null;
                this.trackingService.finishTestout(this.topic_id, this.next, this.start_time, this.weak_questions).subscribe();
                return;
            }
            let current_question_order_no = this.current_question_order_no;
            if (current_question_order_no < 1) { current_question_order_no = 1; }
            if (current_question_order_no > this.lessons_count) { current_question_order_no = this.lessons_count; }
            let questions = this.lessonTree['questions'].filter((obj) => {
                return obj.order_no === current_question_order_no;
            });
            if (questions.length < 1) {
                questions = this.lessonTree['questions'];
            }
            this.question = questions[Math.floor(Math.random() * questions.length)];
            this.lessonTree['questions'] = this.lessonTree['questions'].filter( (obj) => {
                return obj.id !== this.question.id;
            });
            if (this.lesson_id === -1) {
                this.confident_value = null;
                setTimeout(() => {
                    this.confidentChanged();
                });
            }
        } else {
            if (this.randomisation) {
                if (this.question_num > 0 && (this.lessonTree['questions'].length < (this.question_num - this.correct_answers))) {
                    this.lessonTree['questions'].splice(0, this.lessonTree['questions'].length, ...this.all_questions);
                }
                if (!this.isChallenge) {
                    this.question = this.lessonTree['questions'].shift();
                } else {
                    this.question = this.lessonTree['questions'][0];
                }
            } else {
                if (this.current_question_index >= this.lessonTree['questions'].length || this.current_question_index < 0) {
                    this.current_question_index = 0;
                }
                this.question = this.lessonTree['questions'][this.current_question_index];
            }
        }
    }

    checkAnswers() {
        this.correct_answers = 0;
        this.incorrect_answers = 0;
        const checkAnswers = async () => {
            const questionComponents = this.questionComponents.toArray();
            for (let i = 0; i < questionComponents.length; i++) {
                this.question = questionComponents[i].question;
                const isCorrect = await this.isCorrect(questionComponents[i].answers);
                if (isCorrect) {
                    this.correct_answers++;
                } else {
                    this.incorrect_answers++;
                }
            }
        };
        checkAnswers().then( () => {
            if (this.incorrect_answers === 0) {
                this.lessonTree['questions'] = [];
                this.question = null;
                this.trackingService.doneLesson(this.topic_id,
                    this.lesson_id, this.start_time, this.weak_questions, this.fromContentReview).subscribe();
            } else {
                const dialogRef = this.dialog.open(BadChallengeDialogComponent, {
                    position: this.dialogPosition,
                    data: {
                        data: this.correct_answers + ' out of '
                            + (this.correct_answers + this.incorrect_answers) + ' correct',
                        topic_id: this.topic_id,
                        lesson_id: this.lesson_id,
                    }
                });
                dialogRef.afterClosed().subscribe(result => {
                    if (result === 'report') {
                        const question_id = this.all_questions[0].id;
                        const reportDialogRef = this.dialog.open(ReportDialogComponent, {
                            position: this.dialogPosition,
                            data: {question_id: question_id, answers: null}
                        });
                        reportDialogRef.afterClosed().subscribe(res => {
                            this.topicService.reportError(res.question_id,
                                res.answers, res.option, res.text).subscribe();
                        });
                    } else if (result === 'retry') {
                        this.ngOnInit();
                    }
                });
            }
        });
    }

    isCorrect(answers: string[]) {
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

    checkAnswer(answers: string[]) {
        if (this.ignoreAnswer) {
            this.ignoreAnswer = false;
            if (this.lessonTree['questions'].length) {
                this.nextQuestion();
            } else {
                this.question = null;
            }
            return;
        }
        const isCorrect = this.isCorrect(answers);
        if (isCorrect) {
            if (this.lesson_id === -1) {
                this.current_question_order_no += !isNaN(+this.confident_value) ? +this.confident_value : 1;
                if (this.current_question_order_no > this.lessons_count + 2) {
                    this.lessonTree['questions'] = [];
                    this.testout_completed = true;
                }
                this.correct_answers++;
            } else {
                if (!this.randomisation) {
                    this.current_question_index++;
                }
                this.correct_answers++;
                this.complete_percent = (this.correct_answers === 0) ? 0
                    : this.correct_answers / this.question_num * 100;
                // if we have enough correct responses just remove rest of the questions
                if (this.correct_answers === this.question_num && this.question_num !== 0) {
                    this.lessonTree['questions'] = [];
                }
            }
            const goodDialogRef = this.dialog.open(GoodDialogComponent, {
                // width: '800px',
                data: {explanation: this.question.explanation},
                position: this.dialogPosition
            });
            goodDialogRef.afterClosed().subscribe(result => {
                if (result === 'show-explanation' || result === undefined) {
                    this.ignoreAnswer = true;
                    return;
                }
                if (result) {
                    const reportDialogRef = this.dialog.open(FeedbackDialogComponent, {
                        // width: '800px',
                        data: {question_id: this.question.id, answers: this.answers},
                        position: this.dialogPosition
                    });
                    reportDialogRef.afterClosed().subscribe(res => {
                        this.topicService.sendFeedback(res.question_id, res.text).subscribe();
                    });
                }
                if (this.lessonTree['questions'].length) {
                    this.nextQuestion();
                } else {
                    this.question = null;
                    this.trackingService.doneLesson(this.topic_id,
                        this.lesson_id, this.start_time, this.weak_questions, this.fromContentReview).subscribe();
                    if (this.lesson_id === -1) {
                        this.trackingService.finishTestout(this.topic_id, null, this.start_time, this.weak_questions).subscribe();
                    }
                }
            });
        } else {
            if (this.lesson_id === -1) {
                this.current_question_order_no -= 2;
                if (this.current_question_order_no < -1) {
                    this.next = this.question.lesson_id;
                    this.next_title = this.question.lesson_title;
                    this.question = null;
                    return;
                    // this.router.navigate(['/topic/' + this.topic_id + '/lesson/' + this.question.lesson_id]);
                }
                this.incorrect_answers++;
            } else {
                this.randomisation ? this.lessonTree['questions'].push(this.question) : this.current_question_index--;
                if (this.weak_questions.indexOf(this.question.id) === -1) {
                    this.weak_questions.push(this.question.id);
                }
                this.correct_answers = this.complete_percent = 0;
            }
            const dialogRef = this.dialog.open(BadDialogComponent, {
                // width: '800px',
                position: this.dialogPosition,
                data: {
                    data: this.question.answers.filter(function (answer) {
                        if (answer.is_correct === 1) {
                            return true;
                        }
                        return false;
                    }), explanation: this.question.explanation,
                    showAnswers: (this.lesson_id !== -1)
                }
            });
            dialogRef.afterClosed().subscribe(result => {
                if (result === 'show-explanation' || result === undefined) {
                    this.ignoreAnswer = true;
                    return;
                }
                if (result) {
                    const reportDialogRef = this.dialog.open(ReportDialogComponent, {
                        // width: '800px',
                        position: this.dialogPosition,
                        data: {question_id: this.question.id, answers: this.answers}
                    });
                    reportDialogRef.afterClosed().subscribe(res => {
                        this.topicService.reportError(res.question_id,
                            res.answers, res.option, res.text).subscribe();
                    });
                }
                if (this.lessonTree['questions'].length) {
                    this.nextQuestion();
                } else {
                    this.question = null;
                    this.trackingService.doneLesson(this.topic_id,
                        this.lesson_id, this.start_time, this.weak_questions, this.fromContentReview).subscribe();
                }
            });
        }
    }

}
