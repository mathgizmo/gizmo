import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {ActivatedRoute, Router} from '@angular/router';
import * as moment from 'moment';
import {User} from '../../../../_models';
import {UserService, AuthenticationService} from '../../../../_services/index';
import {environment} from '../../../../../environments/environment';
import {MatDialog} from '@angular/material/dialog';
import {DeviceDetectorService} from 'ngx-device-detector';
import {ResearchConsentDialogComponent} from '../research-consent-dialog/research-consent-dialog.component';

@Component({
    selector: 'app-my-assignments',
    templateUrl: './assignments.component.html',
    styleUrls: ['./assignments.component.scss']
})
export class MyAssignmentsComponent implements OnInit, OnDestroy {

    public user: User;
    public classId: number;
    public myClass = {
        id: 0,
        name: '',
        is_researchable: 0,
        pivot: {
            is_consent_read: 0,
            is_element1_accepted: 0,
            is_element2_accepted: 0,
            is_element3_accepted: 0,
            is_element4_accepted: 0
        }
    };

    public backLinkText = 'Back';

    public applications = [];
    public completedApplications = [];
    public selectedAppId = null;
    public showCompletedApplications = false;
    private readonly adminUrl = environment.adminUrl;
    private checkAvailabilityIntervalId = null;

    private sub: any;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(
        private authenticationService: AuthenticationService,
        private userService: UserService,
        private sanitizer: DomSanitizer,
        private router: Router,
        private route: ActivatedRoute,
        public dialog: MatDialog,
        private deviceService: DeviceDetectorService
    ) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.user = this.authenticationService.userValue;
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            this.userService.getClass(this.classId)
                .subscribe(response => {
                    this.myClass = response;
                    this.backLinkText = 'My Classes > ' + (this.myClass ? this.myClass.name : this.classId) + ' > Assignments';
                    if (this.myClass && this.myClass.is_researchable && this.myClass.pivot && !this.myClass.pivot.is_consent_read) {
                        this.dialog.open(ResearchConsentDialogComponent, {
                            data: { 'class_id': this.classId, 'consent': this.myClass.pivot },
                            position: this.dialogPosition
                        });
                    }
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

    onDownload(format = 'csv') {
        this.userService.downloadAssignmentsReport(this.myClass.id, format)
            .subscribe(file => {
                let type = 'text/csv;charset=utf-8;';
                switch (format) {
                    case 'xls':
                    case 'xlsx':
                        type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;';
                        break;
                    default:
                        break;
                }
                const newBlob = new Blob([file], { type: type });
                if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                    window.navigator.msSaveOrOpenBlob(newBlob);
                    return;
                }
                const data = window.URL.createObjectURL(newBlob);
                const link = document.createElement('a');
                link.href = data;
                link.download = this.myClass.name + 'Assignments Report ' + this.user.email + '.' + format;
                link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
                setTimeout(function () {
                    window.URL.revokeObjectURL(data);
                    link.remove();
                }, 100);
            });
    }

}
