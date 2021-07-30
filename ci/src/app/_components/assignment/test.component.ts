import {Component, OnInit, OnDestroy, ViewChildren, QueryList} from '@angular/core';
import {ActivatedRoute, NavigationEnd, Router} from '@angular/router';
import {timer, Subscription} from 'rxjs';
import {takeWhile, tap} from 'rxjs/operators';
import {QuestionComponent} from './topic/lesson/question/question.component';
import {AuthenticationService, QuestionService, TestService, TrackingService} from '../../_services';

@Component({
    selector: 'app-test',
    templateUrl: './test.component.html',
    styleUrls: ['./test.component.scss'],
    providers: [TestService, TrackingService, QuestionService]
})
export class TestComponent implements OnInit, OnDestroy {

    public testId: number;
    public classId: number;
    public test = {
        id: 0,
        name: '',
        duration: null,
        allow_any_order: 0,
        allow_back_tracking: 0,
        has_password: true,
        questions: [],
        complete_percent: 0,
        questions_count: 0,
        answered_questions_count: 0,
        start: 0,
        start_at: null
    };
    public enableTimer = true;
    public counter = 1800;
    private countDown: Subscription;
    private tracker: Subscription;

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
    ) {
    }

    ngOnInit() {
        const user = this.authenticationService.userValue;
        if (user && user.options) {
            this.options = user.options;
        }
        this.sub = this.route.params.subscribe(params => {
            this.testId = +params['test_id'] || 0;
            this.classId = +params['class_id'] || 0;
        });
        this.routerEvent = this.router.events.subscribe((evt) => {
            if (evt instanceof NavigationEnd) {
                this.startTest();
            }
        });
        this.startTest();
    }

    ngOnDestroy() {
        this.routerEvent.unsubscribe();
        if (this.countDown) {
            this.countDown.unsubscribe();
        }
        if (this.tracker) {
            this.tracker.unsubscribe();
        }
    }

    public startTest() {
        this.testService.startTest(this.testId)
            .subscribe(res => {
                if (!res.test) {
                    this.router.navigate(['student/tests']);
                }
                this.initialLoading = 0;
                this.test = res.test;
                this.enableTimer = res.test.duration > 0;
                this.counter = +res.test.time_left;
                if (this.enableTimer) {
                    this.initTimer();
                    this.test.start = Date.now();
                }
                if (this.test.duration > 0) {
                    this.initTracker();
                }
                this.test.questions_count = +res.test.questions_count;
                this.test.answered_questions_count = this.test.questions_count - res.test.questions.length;
                this.test.complete_percent = this.test.questions_count > 0 ?
                    (this.test.answered_questions_count / this.test.questions_count * 100) : 100;
                if (this.test && this.test.questions_count) {
                    if (this.test.allow_any_order) {
                        const shuffle = (array) => {
                            let currentIndex = array.length, temporaryValue, randomIndex;
                            while (0 !== currentIndex) {
                                randomIndex = Math.floor(Math.random() * currentIndex);
                                currentIndex -= 1;
                                temporaryValue = array[currentIndex];
                                array[currentIndex] = array[randomIndex];
                                array[randomIndex] = temporaryValue;
                            }
                            return array;
                        };
                        this.test.questions = shuffle(this.test.questions);
                    }
                    this.nextQuestion();
                }
            }, error => {
                this.router.navigate(['student/tests']);
            });
    }

    private nextQuestion() {
        this.question = this.test.questions.shift();
    }

    public onDoQuestionLater() {
        this.test.questions.push(this.question);
        this.nextQuestion();
    }

    public checkAnswer(answers: string[]) {
        const isCorrect = this.isCorrect(answers);
        this.trackingService.trackQuestionAnswer(this.question.id, isCorrect, this.testId).subscribe(() => {
            this.test.complete_percent = ++this.test.answered_questions_count / this.test.questions_count * 100;
            this.test.questions.length ? this.nextQuestion() : this.finishTest();
        }, error => {
            alert(error);
            this.question = null;
            this.counter = 0;
        });
    }

    private isCorrect(answers: string[]) {
        const isCorrect = this.questionService.isCorrect(this.question, answers);
        this.answers = this.questionService.answers;
        this.question = this.questionService.question;
        return isCorrect;
    }

    private finishTest() {
        if (this.countDown) {
            this.countDown.unsubscribe();
        }
        if (this.tracker) {
            this.tracker.unsubscribe();
        }
        this.testService.finishTest(this.testId).subscribe((res) => {
            this.correctQuestionRate = res['correct_question_rate'];
            this.question = null;
            this.counter = 0;
        });
    }

    private initTimer() {
        this.countDown = timer(0, 1000)
            .pipe(
                takeWhile(() => this.counter > 0),
                tap(() => {
                    --this.counter;
                    if (this.counter <= 0) {
                        this.finishTest();
                    }
                })
            ).subscribe(() => { });
        if (this.counter <= 0) {
            this.finishTest();
        }
    }

    private initTracker() {
        this.tracker = timer(0, 60000) // every 60 sec.
            .pipe(
                takeWhile(() => this.counter > 0),
                tap(() => {
                    this.testService.trackTest(this.testId).subscribe(() => { }, error => {
                        alert('Connection Error: ' + error);
                    });
                })
            ).subscribe(() => { });
    }

}
