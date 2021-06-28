import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {ActivatedRoute, Router} from '@angular/router';
import * as moment from 'moment';
import {UserService} from '../../../../_services/index';
import {environment} from '../../../../../environments/environment';

@Component({
    selector: 'app-my-assignments',
    templateUrl: './assignments.component.html',
    styleUrls: ['./assignments.component.scss'],
    providers: [UserService]
})
export class MyAssignmentsComponent implements OnInit, OnDestroy {
    public classId: number;
    public myClass = {
        id: 0,
        name: ''
    };

    public backLinkText = 'Back';

    public applications = [];
    public completedApplications = [];
    public selectedAppId = null;
    public showCompletedApplications = false;
    private readonly adminUrl = environment.adminUrl;
    private checkAvailabilityIntervalId = null;

    public myClasses = [];
    private sub: any;

    constructor(
        private userService: UserService,
        private sanitizer: DomSanitizer,
        private router: Router,
        private route: ActivatedRoute
    ) { }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            this.userService.getClasses()
                .subscribe(response => {
                    this.myClasses = response['my_classes'];
                    this.myClass = this.myClasses.find(obj => {
                        return obj.id === this.classId;
                    });
                    this.backLinkText = 'My Classes > ' + (this.myClass ? this.myClass.name : this.classId) + ' > Report';
                });
        });
        this.userService.getToDos(this.classId)
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
