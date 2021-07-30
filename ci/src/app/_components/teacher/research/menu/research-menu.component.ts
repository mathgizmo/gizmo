import {Component, Input, OnInit} from '@angular/core';

@Component({
    selector: 'app-research-menu',
    templateUrl: './research-menu.component.html',
    styleUrls: ['./research-menu.component.scss']
})
export class ResearchMenuComponent implements OnInit {

    @Input() classId: number;

    constructor() {
    }

    ngOnInit() {
    }
}
