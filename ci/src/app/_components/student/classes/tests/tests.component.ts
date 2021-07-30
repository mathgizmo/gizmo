import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {ActivatedRoute, Router} from '@angular/router';
import {UserService, AuthenticationService} from '../../../../_services/index';
import {MatDialog} from '@angular/material/dialog';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatSnackBar} from '@angular/material/snack-bar';
import {TestOptionsDialogComponent} from './test-options-dialog/test-options-dialog.component';
import {TestStartDialogComponent} from './test-start-dialog/test-start-dialog.component';
import {TestReportDialogComponent} from './test-report-dialog/test-report-dialog.component';
import {User} from '../../../../_models';
import * as moment from 'moment';
import {environment} from '../../../../../environments/environment';
import {ResearchConsentDialogComponent} from '../research-consent-dialog/research-consent-dialog.component';

@Component({
    selector: 'app-my-tests',
    templateUrl: './tests.component.html',
    styleUrls: ['./tests.component.scss'],
})
export class MyTestsComponent implements OnInit, OnDestroy {

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

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    public myClasses = [];
    private sub: any;

    constructor(
        private authenticationService: AuthenticationService,
        private userService: UserService,
        private sanitizer: DomSanitizer,
        private router: Router,
        private route: ActivatedRoute,
        public dialog: MatDialog,
        private deviceService: DeviceDetectorService,
        public snackBar: MatSnackBar
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
                    this.backLinkText = 'My Classes > ' + (this.myClass ? this.myClass.name : this.classId) + ' > Tests';
                    if (this.myClass && this.myClass.is_researchable && this.myClass.pivot && !this.myClass.pivot.is_consent_read) {
                        this.dialog.open(ResearchConsentDialogComponent, {
                            data: { 'class_id': this.classId, 'consent': this.myClass.pivot },
                            position: this.dialogPosition
                        });
                    }
                });
        });
        this.userService.getTests(this.classId)
            .subscribe(response => {
                this.applications = response.filter(app => !app.is_completed);
                this.completedApplications = response.filter(app => app.is_completed);
            });
        this.checkAvailabilityIntervalId = setInterval(() => {
            this.checkAppsAvailability();
        }, 3000);
    }

    checkAppsAvailability() {
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
    }

    ngOnDestroy() {
        clearInterval(this.checkAvailabilityIntervalId);
    }

    onChangeToDo(app) {
        if (!app || (app.is_blocked)) {
            return;
        }
        if (app.in_progress) {
            return this.router.navigate(['/student/class/' + this.classId + '/test/' + app.class_app_id]);
        }
        return this.openStartTestDialog(app);
    }

    onShowTestReport(app) {
        this.dialog.open(TestReportDialogComponent, {
            data: { test: app },
            position: this.dialogPosition
        });
    }

    openOptionsDialog() {
        this.dialog.open(TestOptionsDialogComponent, {
            data: { },
            position: this.dialogPosition
        });
    }

    openStartTestDialog(test) {
        const dialogRef = this.dialog.open(TestStartDialogComponent, {
            data: { test: test },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                if (test.is_revealed) {
                    this.router.navigate(['/student/class/' + this.classId + '/test/' + test.class_app_id]);
                } else {
                    this.userService.revealTest(test.class_app_id, result)
                        .subscribe(response => {
                            this.router.navigate(['/student/class/' + this.classId + '/test/' + test.class_app_id]);
                        }, error => {
                            let message = '';
                            if (typeof error === 'object') {
                                Object.values(error).forEach(x => {
                                    message += x + ' ';
                                });
                            } else {
                                message = error;
                            }
                            this.snackBar.open(message ? message : 'Unable to open the test!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                }
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

    onDownload(format = 'csv') {
        this.userService.downloadTestsReport(this.myClass.id, format)
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
                link.download = this.myClass.name + ' - Tests Report ' + this.user.email + '.' + format;
                link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
                setTimeout(function () {
                    window.URL.revokeObjectURL(data);
                    link.remove();
                }, 100);
            });
    }

}
