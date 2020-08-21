import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {ActivatedRoute, Router} from '@angular/router';
import * as moment from 'moment';
import {environment} from '../../../../../environments/environment';
import {ClassesManagementService} from '../../../../_services/index';

@Component({
    selector: 'app-class-to-do',
    templateUrl: './class-to-do.component.html',
    styleUrls: ['./class-to-do.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassToDoComponent implements OnInit, OnDestroy {
    public classId: number;
    public applications = [];
    public completedApplications = [];
    public selectedAppId = null;
    public showCompletedApplications = false;
    private readonly adminUrl = environment.adminUrl;
    private checkAvailabilityIntervalId = null;

    private sub: any;

    constructor(
        private classService: ClassesManagementService,
        private sanitizer: DomSanitizer,
        private router: Router,
        private route: ActivatedRoute
    ) { }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            this.classService.getToDos(this.classId)
                .subscribe(response => {
                    this.applications = response.filter(app => !app.is_completed);
                    this.completedApplications = response.filter(app => app.is_completed);
                });
        });
        this.checkAvailabilityIntervalId = setInterval(() => {
            const now = moment();
            this.applications.forEach(app => {
                if (app.start_date || app.due_date) {
                    const start = app.start_date
                        ? moment(app.start_date + ' ' + app.start_time, 'YYYY-MM-DD HH:mm:ss')
                        : null;
                    const due = app.due_date
                        ? moment(app.due_date + ' ' + app.due_time, 'YYYY-MM-DD HH:mm:ss')
                        : null;
                    app.is_blocked = (start && start.isAfter(now)) || (due && due.isBefore(now));
                }
            });
        }, 3000);
    }

    ngOnDestroy() {
        clearInterval(this.checkAvailabilityIntervalId);
    }

    onChangeToDo(app) {
        if (!app || (app.is_blocked)) {
            return;
        }
        localStorage.setItem('app_id', app.id + '');
        this.router.navigate(['/']);
    }

    setIcon(image) {
        if (!image) {
            image = 'images/default-icon.svg';
        }
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}
