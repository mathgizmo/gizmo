import {Component, Input, OnInit} from '@angular/core';
import {ClassesManagementService} from '../../../../_services';

@Component({
    selector: 'app-class-menu',
    templateUrl: './class-menu.component.html',
    styleUrls: ['./class-menu.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassMenuComponent implements OnInit {

    @Input() classId: number;
    public class = {
        id: 0,
        key: null,
        name: '',
        class_type: 'other',
        subscription_type: 'open',
        invitations: ''
    };

    constructor(private classService: ClassesManagementService) {
    }

    ngOnInit() {
        const classes = this.classService.classes;
        this.class = classes.filter(x => +x.id === +this.classId)[0];
    }
}
