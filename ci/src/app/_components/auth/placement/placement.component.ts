/* import {Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';
import {PlacementService} from '../../../_services/index';

@Component({
    selector: 'app-placement',
    templateUrl: './placement.component.html',
    styleUrls: ['./placement.component.scss'],
    providers: [PlacementService]
})
export class PlacementComponent implements OnInit {

    questions: any = [];
    unitId = -1;
    question = '';
    state = 0;

    loading = false;

    constructor(private placementService: PlacementService, private router: Router) {
    }

    ngOnInit() {
    }

    onSkip() {
        this.router.navigate(['/']);
    }

    onNext() {
        this.loading = true;
        this.placementService.getPlacementQuestions()
            .subscribe(response => {
                this.state = 1;
                this.questions = response;
                this.nextQuestion();
                this.loading = false;
            });
    }

    onYes() {
        this.loading = true;
        this.placementService.doneUnit(this.unitId).subscribe(response => {
            this.nextQuestion();
            this.loading = false;
        });
    }

    onNo() {
        this.placementService.getFirstTopicId(this.unitId).subscribe(response => {
            this.router.navigate(['/topic/' + response]);
        });
    }

    onNotSure() {
        this.placementService.doneHalfUnit(this.unitId).subscribe(response => {
            this.router.navigate(['/topic/' + response]);
        });
    }

    nextQuestion() {
        if (this.questions.length) {
            const question = this.questions.shift();
            this.unitId = question['unit_id'];
            this.question = question['question'];
        } else {
            this.state = 2;
        }
    }
} */
