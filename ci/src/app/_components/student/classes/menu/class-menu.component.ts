import {Component, Input, OnInit} from '@angular/core';

@Component({
    selector: 'app-my-class-menu',
    templateUrl: './class-menu.component.html',
    styleUrls: ['./class-menu.component.scss']
})
export class MyClassMenuComponent implements OnInit {

    @Input() classId: number;
    @Input() myClass: any;

    constructor() {
    }

    ngOnInit() {
    }

}
