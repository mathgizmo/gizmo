import {Component, OnInit} from '@angular/core';
import {ClassesManagementService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import * as moment from 'moment';

@Component({
    selector: 'app-class-dashboard',
    templateUrl: './class-dashboard.component.html',
    styleUrls: ['./class-dashboard.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassDashboardComponent implements OnInit {

    public classId: number;

    public class = {
        name: ''
    };

    public assignments = [];
    public tests = [];
    public currentDate = (new Date()).toISOString().split('T')[0];

    public backLinkText = 'Back';

    private sub: any;

    constructor(
        private route: ActivatedRoute,
        private classService: ClassesManagementService) {
    }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            const classes = this.classService.classes;
            this.class = classes.filter(x => x.id === this.classId)[0];
            this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Dashboard';
            this.classService.getAssignments(this.classId)
                .subscribe(res => {
                    this.assignments = res['assignments'];
                    this.assignments.forEach(item => {
                        item.start_time = item.start_time ? moment(item.start_time, 'HH:mm').format('hh:mm A') : '12:00 AM';
                        item.due_time = item.due_time ? moment(item.due_time, 'HH:mm').format('hh:mm A') : '12:00 AM';
                    });
                });
            this.classService.getTests(this.classId)
                .subscribe(res => {
                    this.tests = res['tests'];
                    this.tests.forEach(item => {
                        item.start_time = item.start_time ? moment(item.start_time, 'HH:mm').format('hh:mm A') : '12:00 AM';
                        item.due_time = item.due_time ? moment(item.due_time, 'HH:mm').format('hh:mm A') : '12:00 AM';
                    });
                });
        });
    }
}
