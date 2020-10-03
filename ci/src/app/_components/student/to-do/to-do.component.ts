import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {Router} from '@angular/router';
import * as moment from 'moment';
import {UserService} from '../../../_services/user.service';
import {environment} from '../../../../environments/environment';

@Component({
    selector: 'app-to-do',
    templateUrl: './to-do.component.html',
    styleUrls: ['./to-do.component.scss'],
    providers: [UserService]
})
export class ToDoComponent implements OnInit, OnDestroy {
    public applications = [];
    public completedApplications = [];
    public selectedAppId = null;
    public showCompletedApplications = false;
    private readonly adminUrl = environment.adminUrl;
    private checkAvailabilityIntervalId = null;

    constructor(
        private userService: UserService,
        private sanitizer: DomSanitizer,
        private router: Router
    ) { }

    ngOnInit() {
        this.userService.getToDos()
            .subscribe(response => {
                this.applications = response.filter(app => !app.is_completed);
                this.completedApplications = response.filter(app => app.is_completed);
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
        this.router.navigate(['/assignment/' + app.id]);
    }

    setIcon(image) {
        if (!image) {
            image = 'images/default-icon.svg';
        }
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}
