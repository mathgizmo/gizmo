import {Component, OnInit, OnDestroy, ViewChildren, QueryList } from '@angular/core';
import {ActivatedRoute, NavigationEnd, Router} from '@angular/router';
import { timer, Subscription } from 'rxjs';
import {takeWhile, tap} from 'rxjs/operators';
import { QuestionComponent } from './topic/lesson/question/question.component';
import {AuthenticationService, QuestionService, TestService, TrackingService} from '../../_services';

@Component({
    selector: 'app-test',
    templateUrl: './test.component.html',
    styleUrls: ['./test.component.scss'],
    providers: [TestService, TrackingService, QuestionService, AuthenticationService]
})
export class TestComponent implements OnInit, OnDestroy {

    public testId: number;
    public test = {
        id: 0,
        name: '',
        duration: null,
        allow_any_order: false,
        has_password: true,
        questions: [],
        complete_percent: 0,
        questions_count: 0,
        answered_questions_count: 0,
        start: 0
    };
    public enableTimer = true;
    public counter = 1800;
    private countDown: Subscription;

    public options = {
        is_test_timer_displayed: 1,
        is_test_questions_count_displayed: 1
    };

    public backLinkText = 'Back';
    public initialLoading = 1;

    public question: any = null;
    public answers: any;

    public correctQuestionRate = null;

    @ViewChildren(QuestionComponent)
    private questionComponents: QueryList<QuestionComponent>;

    private routerEvent;
    private sub: any;

    constructor(private router: Router,
                private route: ActivatedRoute,
                private testService: TestService,
                private trackingService: TrackingService,
                private questionService: QuestionService,
                private authenticationService: AuthenticationService,
    ) {}

    ngOnInit() {
        const user = this.authenticationService.userValue;
        if (user && user.options) {
            this.options = user.options;
        }
        this.sub = this.route.params.subscribe(params => {
            this.testId = +params['test_id'] || 0;
        });
        this.routerEvent = this.router.events.subscribe((evt) => {
            if (evt instanceof NavigationEnd) {
                this.initData();
            }
        });
        this.initData();
    }

    ngOnDestroy() {
        this.routerEvent.unsubscribe();
        if (this.countDown) {
            this.countDown.unsubscribe();
        }
    }

    initData() {
        if (this.checkState()) {
            this.initialLoading = 0;
            this.enableTimer = this.test.duration > 0;
            if (this.enableTimer) {
                this.counter = this.test.duration - ((Date.now() - this.test.start) / 1000);
                this.initTimer();
            }
            if (this.test.questions_count) {
                this.nextQuestion();
            }
        } else {
            this.testService.startTest(this.testId)
                .subscribe(res => {
                    this.initialLoading = 0;
                    this.test = res.test;
                    this.counter = res.test.duration;
                    this.enableTimer = this.test.duration > 0;
                    if (this.enableTimer) {
                        this.initTimer();
                        this.test.start = Date.now();
                    }
                    this.test.questions_count = +res.test.questions.length;
                    this.test.complete_percent = this.test.answered_questions_count = 0;
                    if (this.test && this.test.questions_count) {
                        this.nextQuestion();
                    }
                }, error => {
                    this.router.navigate(['student/tests']);
                });
        }
    }

    nextQuestion() {
        this.saveState();
        this.question = this.test.questions.shift();
    }

    onDoQuestionLater() {
        this.test.questions.push(this.question);
        this.nextQuestion();
    }

    checkAnswer(answers: string[]) {
        const isCorrect = this.isCorrect(answers);
        this.trackingService.trackQuestionAnswer(this.question.id, isCorrect, null, this.testId).subscribe(() => {
            this.test.complete_percent = ++this.test.answered_questions_count / this.test.questions_count * 100;
            this.test.questions.length ? this.nextQuestion() : this.finishTest();
        });
    }

    isCorrect(answers: string[]) {
        const isCorrect = this.questionService.isCorrect(this.question, answers);
        this.answers = this.questionService.answers;
        this.question = this.questionService.question;
        return isCorrect;
    }

    finishTest() {
        if (this.countDown) {
            this.countDown.unsubscribe();
        }
        this.testService.finishTest(this.testId).subscribe((res) => {
            this.correctQuestionRate = res['correct_question_rate'];
            this.question = null;
            this.counter = 0;
            this.removeState();
        });
    }

    private initTimer() {
        this.countDown = timer(0, 1000)
            .pipe(
                takeWhile( () => this.counter > 0),
                tap(() => {
                    --this.counter;
                    if (this.counter <= 0) {
                        this.finishTest();
                    }
                })
            )
            .subscribe( () => {});
        if (this.counter <= 0) {
            this.finishTest();
        }
    }

    private checkState() {
        const test = localStorage.getItem('current_test');
        if (test) {
            this.test = JSON.parse(test);
        }
        const question = localStorage.getItem('current_question');
        if (question) {
            this.question = JSON.parse(question);
        }
        return !!test;
    }

    private saveState() {
        localStorage.setItem('current_test', JSON.stringify(this.test));
        localStorage.setItem('current_question', JSON.stringify(this.question));
    }

    private removeState() {
        localStorage.removeItem('current_test');
        localStorage.removeItem('current_question');
    }

}
