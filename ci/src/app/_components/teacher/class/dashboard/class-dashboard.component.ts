import {Component, OnInit} from '@angular/core';
import {ClassesManagementService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import {Sort} from '@angular/material/sort';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
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
    public currentDate = (new Date()).toISOString().split('T')[0];
    private readonly adminUrl = environment.adminUrl;

    public backLinkText = 'Back';

    private sub: any;

    constructor(
        private route: ActivatedRoute,
        private classService: ClassesManagementService,
        private sanitizer: DomSanitizer) {
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
                    this.updateStatuses();
                });
        });
    }

    updateStatuses() {
        const now = moment();
        this.assignments.forEach(app => {
            if (app.start_date || app.due_date) {
                const start = app.start_date
                    ? moment(app.start_date + ' ' + app.start_time, 'YYYY-MM-DD HH:mm:ss')
                    : null;
                const due = app.due_date
                    ? moment(app.due_date + ' ' + app.due_time, 'YYYY-MM-DD HH:mm:ss')
                    : null;
                app.status = (start && start.isAfter(now)) ? 'Upcoming' :
                    (due && due.isBefore(now)) ? 'Complete' : 'In progress';
            } else {
                app.status = 'In progress';
            }
        });
    }

    sortData(sort: Sort) {
        const data = this.assignments.slice();
        if (!sort.active || sort.direction === '') {
            this.assignments = data;
            return;
        }
        this.assignments = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'start_date': return compare(a.start_date, b.start_date, isAsc);
                case 'start_time': return compare(a.start_time, b.start_time, isAsc);
                case 'due_date': return compare(a.due_date, b.due_date, isAsc);
                case 'due_time': return compare(a.due_time, b.due_time, isAsc);
                default: return 0;
            }
        });
    }

    setIcon(image) {
        if (!image) {
            image = 'images/default-icon.svg';
        }
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    if (typeof a === 'string' || typeof b === 'string') {
        a = ('' + a).toLowerCase();
        b = ('' + b).toLowerCase();
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
