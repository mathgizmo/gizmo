import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {Router} from '@angular/router';
import * as moment from 'moment';
import {UserService} from '../../../_services/user.service';
import {environment} from '../../../../environments/environment';

@Component({
    selector: 'app-to-do',
    templateUrl: './to-do.component.html',
    styleUrls: ['./to-do.component.scss']
})
export class ToDoComponent implements OnInit, OnDestroy {
    public applications = [];
    public completedApplications = [];
    public selectedAppId = null;
    public showCompletedApplications = false;
    public requireTags = false;
    private userTagIds: number[] = [];
    private readonly adminUrl = environment.adminUrl;
    private checkAvailabilityIntervalId = null;

    constructor(
        private userService: UserService,
        private sanitizer: DomSanitizer,
        private router: Router
    ) { }

    ngOnInit() {
        // Load profile to check if user has selected any interest tags.
        this.userService.getProfile().subscribe(profile => {
            const tags = (profile && profile['tags']) || [];
            if (!tags || tags.length === 0) {
                this.requireTags = true;
                return;
            }
            this.userTagIds = tags.map(t => +t.id);
            this.loadToDos();
        }, _ => {
            // On error, default to requiring tags to avoid showing content prematurely
            this.requireTags = true;
        });
    }

    private loadToDos() {
        this.userService.getToDos(null, true)
            .subscribe(response => {
                const filterByTags = (list: any[]) => {
                    if (!this.userTagIds || this.userTagIds.length === 0) { return list; }
                    return list.filter(app => {
                        const tags = (app.tags || []).map(t => +t.id);
                        // keep if overlap
                        return tags.some(id => this.userTagIds.indexOf(id) !== -1);
                    });
                };
                this.applications = filterByTags(response.filter(app => !app.is_completed));
                this.completedApplications = filterByTags(response.filter(app => app.is_completed));
                // Start availability checker only after data loaded
                this.startAvailabilityChecker();
            });
    }

    private startAvailabilityChecker() {
        if (this.checkAvailabilityIntervalId) { return; }
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
        if (!app || (app.is_blocked && !app.is_completed)) {
            return;
        }
        localStorage.setItem('app_id', app.id + '');
        this.router.navigate(['/assignment/' + (app.class_app_id || -1)]);
    }

    setIcon(image) {
        if (!image) {
            image = 'images/default-icon.svg';
        }
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}
