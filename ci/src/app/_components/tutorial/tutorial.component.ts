import {Component, OnInit} from '@angular/core';
import {TutorialService} from '../../_services/tutorial.service';

@Component({
    selector: 'app-tutorial',
    templateUrl: './tutorial.component.html',
    styleUrls: ['./tutorial.component.scss'],
    providers: [TutorialService]
})
export class TutorialComponent implements OnInit {

    public tutorials = [];

    constructor(private tutorialService: TutorialService) {}

    ngOnInit() {
        this.tutorialService.getTutorials()
            .subscribe(res => {
                this.tutorials = res;
            });
    }

}
