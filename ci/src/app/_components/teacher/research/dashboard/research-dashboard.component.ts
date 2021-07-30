import {Component, OnInit} from '@angular/core';
import {ClassesManagementService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import * as moment from 'moment';

@Component({
    selector: 'app-research-dashboard',
    templateUrl: './research-dashboard.component.html',
    styleUrls: ['./research-dashboard.component.scss']
})
export class ResearchDashboardComponent implements OnInit {

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
            this.classService.getClass(this.classId).subscribe(res => {
                this.class = res;
                this.backLinkText = 'Research > ' + (this.class ? this.class.name : this.classId) + ' > Dashboard';
            });
            this.classService.getAssignments(this.classId, { for_research: 1 })
                .subscribe(res => {
                    this.assignments = res['assignments'];
                    this.assignments.forEach(item => {
                        item.start_time = item.start_time ? moment(item.start_time, 'HH:mm').format('hh:mm A') : '12:00 AM';
                        item.due_time = item.due_time ? moment(item.due_time, 'HH:mm').format('hh:mm A') : '12:00 AM';
                    });
                });
            this.classService.getTests(this.classId, { for_research: 1 })
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
