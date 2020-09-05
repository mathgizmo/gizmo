import {Component, Input, OnInit} from '@angular/core';

@Component({
    selector: 'app-class-menu',
    templateUrl: './class-menu.component.html',
    styleUrls: ['./class-menu.component.scss'],
})
export class ClassMenuComponent implements OnInit {

    @Input() classId: number;

    constructor() {
    }

    ngOnInit() {
    }

}
