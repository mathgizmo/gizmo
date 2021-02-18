import {Component, OnInit, ViewChild} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {MatDialog} from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import * as moment from 'moment';

import {ClassesManagementService} from '../../../../_services';
import {DeviceDetectorService} from 'ngx-device-detector';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {ActivatedRoute} from '@angular/router';
import {EditClassTestDialogComponent} from './edit-test-dialog/edit-class-test-dialog.component';
import {SelectStudentsDialogComponent} from '../assignments/select-students-dialog/select-students-dialog.component';
import {TestReportDialogComponent} from './test-report-dialog/test-report-dialog.component';
import {ClassAssignmentsCalendarComponent} from '../assignments/calendar/class-assignments-calendar.component';
import {DeleteConfirmationDialogComponent, YesNoDialogComponent} from '../../../dialogs/index';

@Component({
    selector: 'app-class-tests',
    templateUrl: './class-tests.component.html',
    styleUrls: ['./class-tests.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassTestsComponent implements OnInit {

    classId: number;

    tests = [];
    available_tests = [];
    class = {
        name: ''
    };
    addTest = false;
    nameFilter;
    students = [];

    private readonly adminUrl = environment.adminUrl;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    public calendarView = false;

    public backLinkText = 'Back';

    private sub: any;

    @ViewChild('calendar') calendarComponent: ClassAssignmentsCalendarComponent;

    constructor(
        private route: ActivatedRoute,
        public snackBar: MatSnackBar,
        public dialog: MatDialog, private deviceService: DeviceDetectorService,
        private classService: ClassesManagementService,
        private sanitizer: DomSanitizer) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            this.classService.getTests(this.classId)
                .subscribe(res => {
                    const classes = this.classService.classes;
                    this.class = classes.filter(x => +x.id === +this.classId)[0];
                    this.available_tests = res['available_tests'];
                    this.tests = res['tests'];
                    this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Tests';
                });
            this.classService.getStudents(this.classId, false).subscribe(students => {
                this.students = students;
            });
        });
    }

    onTestDateChanged(event) {
        const items = this.tests.filter(x => +x.id === +event.id);
        if (items.length > 0) {
            const item = items[0];
            const start = moment(event.start);
            const end = moment(event.end);
            item.start_date = start.format('YYYY-MM-DD');
            item.start_time = start.format('HH:mm');
            item.due_date = end.format('YYYY-MM-DD') < '2100-01-01' ? end.format('YYYY-MM-DD') : null;
            item.due_time = end.format('HH:mm');
            this.classService.changeTest(this.classId, item)
                .subscribe(tests => {
                    this.snackBar.open('Test have been successfully moved!', '', {
                        duration: 3000,
                        panelClass: ['success-snackbar']
                    });
                }, error => {
                    this.snackBar.open('Error occurred while moving test!', '', {
                        duration: 3000,
                        panelClass: ['error-snackbar']
                    });
                });
        }
    }

    onTestAddClicked(event) {
        const start = moment(event.start);
        const end = moment(event.end);
        const item = {
            id: null,
            name: null,
            icon: null,
            start_date: event.start ? start.format('YYYY-MM-DD') : null,
            start_time: event.start ? start.format('HH:mm') : null,
            due_date: event.end ? end.format('YYYY-MM-DD') : null,
            due_time: event.end ? end.format('HH:mm') : null,
            duration: 0,
            attempts: 1,
            password: '',
            color: '#7FA5C1',
            delete: false
        };
        const dialogRef = this.dialog.open(EditClassTestDialogComponent, {
            data: { 'title': 'Add Test', 'test': item, 'available_tests': this.available_tests },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(app => {
            if (app) {
                this.classService.addTestToClass(this.classId, app.id)
                    .subscribe(res1 => {
                        this.classService.changeTest(this.classId, app).subscribe(res2 => {
                            this.tests.unshift(app);
                            this.available_tests = this.available_tests.filter(x => {
                                return +x.id !== +app.id;
                            });
                            this.calendarComponent.updateCalendarEvents();
                            this.snackBar.open('Test have been successfully added!', '', {
                                duration: 3000,
                                panelClass: ['success-snackbar']
                            });
                        }, error => {
                            let message = '';
                            if (typeof error === 'object') {
                                Object.values(error).forEach(x => {
                                    message += x + ' ';
                                });
                            } else {
                                message = error;
                            }
                            this.snackBar.open(message ? message : 'Error occurred while adding test!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                    }, error => {
                        let message = '';
                        if (typeof error === 'object') {
                            Object.values(error).forEach(x => {
                                message += x + ' ';
                            });
                        } else {
                            message = error;
                        }
                        this.snackBar.open(message ? message : 'Error occurred while adding test!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onTestEditClicked(appId) {
        const item = this.tests.filter(x => +x.id === +appId)[0];
        const dialogRef = this.dialog.open(EditClassTestDialogComponent, {
            data: { 'title': 'Edit Test', 'test': item, 'available_tests': this.available_tests },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(app => {
            if (app) {
                if (app.delete) {
                    this.classService.deleteTestFromClass(this.classId, app.id)
                        .subscribe(response => {
                            this.available_tests.unshift(app);
                            this.tests = this.tests.filter(x => {
                                return +x.id !== +item.id;
                            });
                            setTimeout(() => {
                                this.calendarComponent.updateCalendarEvents();
                            }, 10);
                            this.snackBar.open('Test have been successfully deleted!', '', {
                                duration: 3000,
                                panelClass: ['success-snackbar']
                            });
                        }, error => {
                            let message = '';
                            if (typeof error === 'object') {
                                Object.values(error).forEach(x => {
                                    message += x + ' ';
                                });
                            } else {
                                message = error;
                            }
                            this.snackBar.open(message ? message : 'Error occurred while deleting test!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                } else {
                    this.classService.changeTest(this.classId, app)
                        .subscribe(response => {
                            this.calendarComponent.updateCalendarEvents();
                            this.snackBar.open('Test have been successfully updated!', '', {
                                duration: 3000,
                                panelClass: ['success-snackbar']
                            });
                        }, error => {
                            let message = '';
                            if (typeof error === 'object') {
                                Object.values(error).forEach(x => {
                                    message += x + ' ';
                                });
                            } else {
                                message = error;
                            }
                            this.snackBar.open(message ? message : 'Error occurred while updating test!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                }
            }
        });
    }

    onAddTest(app) {
        const dialogRef = this.dialog.open(YesNoDialogComponent, {
            data: { 'message': 'For all students?'},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(forAll => {
            if (!forAll) {
                const dialogRef2 = this.dialog.open(SelectStudentsDialogComponent, {
                    data: { 'students': this.students },
                    position: this.dialogPosition
                });
                dialogRef2.afterClosed().subscribe(students => {
                    if (!students || students.length < 1) { return; }
                    this.classService.addTestToClass(this.classId, app.id, students)
                        .subscribe(newApp => {
                            app.password = newApp.password || '';
                            app.duration = newApp.duration || 0;
                            app.attempts = newApp.attempts || 1;
                            app.start_date = newApp.start_date;
                            app.start_time = newApp.start_time;
                            app.is_for_selected_students = true;
                            app.class_id = newApp.class_id;
                            app.app_id = newApp.app_id;
                            app.students = students;
                            this.tests.unshift(app);
                            this.available_tests = this.available_tests.filter(x => {
                                return +x.id !== +app.id;
                            });
                            this.addTest = !this.addTest;
                            this.snackBar.open('Test have been successfully added!', '', {
                                duration: 3000,
                                panelClass: ['success-snackbar']
                            });
                        }, error => {
                            let message = '';
                            if (typeof error === 'object') {
                                Object.values(error).forEach(x => {
                                    message += x + ' ';
                                });
                            } else {
                                message = error;
                            }
                            this.snackBar.open(message ? message : 'Error occurred while adding test!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                });
            } else {
                this.classService.addTestToClass(this.classId, app.id)
                    .subscribe(newApp => {
                        app.password = newApp.password || '';
                        app.duration = newApp.duration || 0;
                        app.attempts = newApp.attempts || 1;
                        app.start_date = newApp.start_date;
                        app.start_time = newApp.start_time;
                        app.is_for_selected_students = false;
                        app.class_id = newApp.class_id;
                        app.app_id = newApp.app_id;
                        this.tests.unshift(app);
                        this.available_tests = this.available_tests.filter(x => {
                            return +x.id !== +app.id;
                        });
                        this.addTest = !this.addTest;
                        this.snackBar.open('Test have been successfully added!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                    }, error => {
                        let message = '';
                        if (typeof error === 'object') {
                            Object.values(error).forEach(x => {
                                message += x + ' ';
                            });
                        } else {
                            message = error;
                        }
                        this.snackBar.open(message ? message : 'Error occurred while adding test!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onDueDateChanged(item, newDate) {
        item.due_date = newDate;
        this.classService.changeTest(this.classId, item)
            .subscribe(tests => {
                this.snackBar.open('Due Date Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving Due Date!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onDueTimeChanged(item, newTime) {
        item.due_time = newTime;
        this.classService.changeTest(this.classId, item)
            .subscribe(tests => {
                this.snackBar.open('Due Time Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving Due Time!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onStartDateChanged(item, newStartDate) {
        item.start_date = newStartDate;
        this.classService.changeTest(this.classId, item)
            .subscribe(tests => {
                this.snackBar.open('Start Date Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving Start Date!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onStartTimeChanged(item, newStartTime) {
        item.start_time = newStartTime;
        if (!item.start_date) {
            item.start_date = (new Date()).toISOString().split('T')[0];
        }
        this.classService.changeTest(this.classId, item)
            .subscribe(tests => {
                this.snackBar.open('Start Time Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving Start Time!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onDurationChanged(item, newDuration) {
        item.duration = newDuration;
        this.classService.changeTest(this.classId, item)
            .subscribe(tests => {
                this.snackBar.open('Duration Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving Duration!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onPasswordChanged(item, newPassword) {
        item.password = newPassword;
        this.classService.changeTest(this.classId, item)
            .subscribe(tests => {
                this.snackBar.open('Password Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving Password!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onAttemptsChanged(item, newAttempts) {
        item.attempts = newAttempts;
        this.classService.changeTest(this.classId, item)
            .subscribe(tests => {
                this.snackBar.open('Attempts Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving attempts!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onDeleteTest(item) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                // 'message': 'Are you sure that you want to delete this tests from the class?'
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.deleteTestFromClass(this.classId, item.id)
                    .subscribe(response => {
                        this.available_tests.unshift(item);
                        this.tests  = this.tests.filter(x => {
                            return +x.id !== +item.id;
                        });
                        this.snackBar.open('Test have been successfully deleted!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                    }, error => {
                        let message = '';
                        if (typeof error === 'object') {
                            Object.values(error).forEach(x => {
                                message += x + ' ';
                            });
                        } else {
                            message = error;
                        }
                        this.snackBar.open(message ? message : 'Error occurred while deleting test!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onShowTestReport(item) {
        const dialogRef = this.dialog.open(TestReportDialogComponent, {
            data: {
                title: item.name + ': report',
                test: item
            },
            position: this.dialogPosition
        });
    }

    onShowTestStudents(item) {
        const dialogRef = this.dialog.open(SelectStudentsDialogComponent, {
            data: {
                'title': item.name + ': assigned students',
                'students': this.students,
                'selected_students': item.students
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(students => {
            if (!students || students.length < 1) { return; }
            this.classService.changeTestStudents(this.classId, item.id, students)
                .subscribe(response => {
                    item.students = students;
                    this.snackBar.open('Assigned students have been successfully changed!', '', {
                        duration: 3000,
                        panelClass: ['success-snackbar']
                    });
                }, error => {
                    let message = '';
                    if (typeof error === 'object') {
                        Object.values(error).forEach(x => {
                            message += x + ' ';
                        });
                    } else {
                        message = error;
                    }
                    this.snackBar.open(message ? message : 'Error occurred while changing assigned students!', '', {
                        duration: 3000,
                        panelClass: ['error-snackbar']
                    });
                });
        });
    }

    sortData(sort: Sort) {
        const data = this.tests.slice();
        if (!sort.active || sort.direction === '') {
            this.tests = data;
            return;
        }
        this.tests = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'start_date': return compare(a.start_date, b.start_date, isAsc);
                case 'start_time': return compare(a.start_time, b.start_time, isAsc);
                case 'due_date': return compare(a.due_date, b.due_date, isAsc);
                case 'due_time': return compare(a.due_time, b.due_time, isAsc);
                case 'duration': return compare(a.duration, b.duration, isAsc);
                case 'attempts': return compare(a.attempts, b.attempts, isAsc);
                default: return 0;
            }
        });
    }

    sortAvailableTests(sort: Sort) {
        const data = this.available_tests.slice();
        if (!sort.active || sort.direction === '') {
            this.available_tests = data;
            return;
        }
        this.available_tests = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
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
