import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {Router} from '@angular/router';
import * as moment from 'moment';
import {UserService} from '../../../_services/user.service';
import {environment} from '../../../../environments/environment';
import {MatDialog} from '@angular/material/dialog';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatSnackBar} from '@angular/material/snack-bar';
import {TestOptionsDialogComponent} from './test-options-dialog/test-options-dialog.component';

@Component({
    selector: 'app-my-tests',
    templateUrl: './tests.component.html',
    styleUrls: ['./tests.component.scss'],
    providers: [UserService]
})
export class MyTestsComponent implements OnInit, OnDestroy {
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

    // public currentTest = null;
    public password = null;

    constructor(
        private userService: UserService,
        private sanitizer: DomSanitizer,
        private router: Router,
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
        // const test = localStorage.getItem('current_test');
        // if (test) {
        //     this.currentTest = JSON.parse(test);
        // }
        this.userService.getTests()
            .subscribe(response => {
                this.applications = response.filter(app => !app.is_completed);
                this.completedApplications = response.filter(app => app.is_completed);
                // if (this.currentTest) {
                //     this.checkAppsAvailability();
                // }
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
            // if (this.currentTest) {
            //     app.is_blocked = app.class_app_id !== this.currentTest.id;
            // }
        });
    }

    ngOnDestroy() {
        clearInterval(this.checkAvailabilityIntervalId);
    }

    onChangeToDo(app) {
        if (!app || (app.is_blocked)) {
            return;
        }
        this.router.navigate(['/test/' + app.class_app_id]);
    }

    onStartSecretTest() {
        this.userService.revealTest(this.password)
            .subscribe(response => {
                this.router.navigate(['/test/' + response.class_app_id]);
            }, error => {
                let message = '';
                if (typeof error === 'object') {
                    Object.values(error).forEach(x => {
                        message += x + ' ';
                    });
                } else {
                    message = error;
                }
                this.snackBar.open(message ? message : 'Unable to open secret test!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    setIcon(image) {
        if (!image) {
            image = 'images/default-icon.svg';
        }
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

    openOptionsDialog() {
        const dialogRef = this.dialog.open(TestOptionsDialogComponent, {
            data: { },
            position: this.dialogPosition
        });
    }

}
