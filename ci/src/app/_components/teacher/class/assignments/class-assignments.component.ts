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
import {EditClassAssignmentDialogComponent} from './edit-assignment-dialog/edit-class-assignment-dialog.component';
import {SelectStudentsDialogComponent} from './select-students-dialog/select-students-dialog.component';
import {ClassAssignmentsCalendarComponent} from './calendar/class-assignments-calendar.component';
import {DeleteConfirmationDialogComponent, YesNoDialogComponent} from '../../../dialogs/index';
import {AssignmentReportDialogComponent} from './assignment-report-dialog/assignment-report-dialog.component';
import {compare} from '../../../../_helpers/compare.helper';

@Component({
    selector: 'app-class-assignments',
    templateUrl: './class-assignments.component.html',
    styleUrls: ['./class-assignments.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassAssignmentsComponent implements OnInit {

    classId: number;

    assignments = [];
    available_assignments = [];
    class = {
        id: 0,
        name: ''
    };
    addAssignment = false;
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
            this.classService.getAssignments(this.classId)
                .subscribe(res => {
                    const classes = this.classService.classes;
                    this.class = classes.filter(x => +x.id === +this.classId)[0];
                    this.available_assignments = res['available_assignments'];
                    this.assignments = res['assignments'];
                    this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Assignments';
                });
            this.classService.getStudents(this.classId).subscribe(students => {
                this.students = students;
            });
        });
    }

    onAssignmentDateChanged(event) {
        const items = this.assignments.filter(x => +x.id === +event.id);
        if (items.length > 0) {
            const item = items[0];
            const start = moment(event.start);
            const end = moment(event.end);
            item.start_date = start.format('YYYY-MM-DD');
            item.start_time = start.format('HH:mm');
            item.due_date = end.format('YYYY-MM-DD') < '2100-01-01' ? end.format('YYYY-MM-DD') : null;
            item.due_time = end.format('HH:mm');
            this.classService.changeAssignment(this.classId, item)
                .subscribe(assignments => {
                    this.snackBar.open('Assignment have been successfully moved!', '', {
                        duration: 3000,
                        panelClass: ['success-snackbar']
                    });
                }, error => {
                    this.snackBar.open('Error occurred while moving assignment!', '', {
                        duration: 3000,
                        panelClass: ['error-snackbar']
                    });
                });
        }
    }

    onAssignmentAddClicked(event) {
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
            color: '#7FA5C1'
        };
        const dialogRef = this.dialog.open(EditClassAssignmentDialogComponent, {
            data: { 'title': 'Add Assignment', 'assignment': item, 'available_assignments': this.available_assignments },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(app => {
            if (app) {
                this.classService.addAssignmentToClass(this.classId, app.id)
                    .subscribe(res1 => {
                        this.classService.changeAssignment(this.classId, app).subscribe(res2 => {
                            this.assignments.unshift(app);
                            this.available_assignments = this.available_assignments.filter(x => {
                                return +x.id !== +app.id;
                            });
                            this.calendarComponent.updateCalendarEvents();
                            this.snackBar.open('Assignment have been successfully added!', '', {
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
                            this.snackBar.open(message ? message : 'Error occurred while adding assignment!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while adding assignment!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onAssignmentEditClicked(appId) {
        const item = this.assignments.filter(x => +x.id === +appId)[0];
        const dialogRef = this.dialog.open(EditClassAssignmentDialogComponent, {
            data: { 'title': 'Edit Assignment', 'assignment': item, 'available_assignments': this.available_assignments },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(app => {
            if (app) {
                if (app.delete) {
                    this.classService.deleteAssignmentFromClass(this.classId, app.id)
                        .subscribe(response => {
                            this.available_assignments.unshift(app);
                            this.assignments = this.assignments.filter(x => {
                                return +x.id !== +item.id;
                            });
                            setTimeout(() => {
                                this.calendarComponent.updateCalendarEvents();
                            }, 10);
                            this.snackBar.open('Assignment have been successfully deleted!', '', {
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
                            this.snackBar.open(message ? message : 'Error occurred while deleting assignment!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                } else {
                    this.classService.changeAssignment(this.classId, app)
                        .subscribe(response => {
                            this.calendarComponent.updateCalendarEvents();
                            this.snackBar.open('Assignment have been successfully updated!', '', {
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
                            this.snackBar.open(message ? message : 'Error occurred while updating assignment!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                }
            }
        });
    }

    onAddAssignment(app) {
        const dialogRef = this.dialog.open(YesNoDialogComponent, {
            data: { 'message': 'For all students?'},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(forAll => {
            if (!forAll) {
                const dialogRef2 = this.dialog.open(SelectStudentsDialogComponent, {
                    data: { 'students': this.students},
                    position: this.dialogPosition
                });
                dialogRef2.afterClosed().subscribe(students => {
                    if (!students || students.length < 1) { return; }
                    this.classService.addAssignmentToClass(this.classId, app.id, students)
                        .subscribe(newApp => {
                            app.start_date = newApp.start_date;
                            app.start_time = newApp.start_time;
                            app.is_for_selected_students = true;
                            app.students = students;
                            this.assignments.unshift(app);
                            this.available_assignments = this.available_assignments.filter(x => {
                                return +x.id !== +app.id;
                            });
                            this.addAssignment = !this.addAssignment;
                            this.snackBar.open('Assignment have been successfully added!', '', {
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
                            this.snackBar.open(message ? message : 'Error occurred while adding assignment!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                });
            } else {
                this.classService.addAssignmentToClass(this.classId, app.id)
                    .subscribe(newApp => {
                        app.start_date = newApp.start_date;
                        app.start_time = newApp.start_time;
                        app.is_for_selected_students = false;
                        this.assignments.unshift(app);
                        this.available_assignments = this.available_assignments.filter(x => {
                            return +x.id !== +app.id;
                        });
                        this.addAssignment = !this.addAssignment;
                        this.snackBar.open('Assignment have been successfully added!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while adding assignment!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onDueDateChanged(item, newDate) {
        item.due_date = newDate;
        this.classService.changeAssignment(this.classId, item)
            .subscribe(assignments => {
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
        this.classService.changeAssignment(this.classId, item)
            .subscribe(assignments => {
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
        this.classService.changeAssignment(this.classId, item)
            .subscribe(assignments => {
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
        this.classService.changeAssignment(this.classId, item)
            .subscribe(assignments => {
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

    onDeleteAssignment(item) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                // 'message': 'Are you sure that you want to delete this assignments from the class?'
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.deleteAssignmentFromClass(this.classId, item.id)
                    .subscribe(response => {
                        this.available_assignments.unshift(item);
                        this.assignments  = this.assignments.filter(x => {
                            return +x.id !== +item.id;
                        });
                        this.snackBar.open('Assignment have been successfully deleted!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while deleting assignment!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onShowAssignmentStudents(item) {
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
            this.classService.changeAssignmentStudents(this.classId, item.id, students)
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

    onShowAssignmentReport(item) {
        this.classService.getReport(this.classId)
            .subscribe(response => {
                const students = response.students;
                const dialogRef = this.dialog.open(AssignmentReportDialogComponent, {
                    data: {
                        title: item.name + ': report',
                        assignment: item,
                        students: students
                    },
                    position: this.dialogPosition
                });
            });
    }

    onDownload(format = 'csv') {
        this.classService.downloadAssignmentsReport(this.class.id, format)
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
                link.download = this.class.name + ' - Assignments Report.' + format;
                link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
                setTimeout(function () {
                    window.URL.revokeObjectURL(data);
                    link.remove();
                }, 100);
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

    sortAvailableAssignments(sort: Sort) {
        const data = this.available_assignments.slice();
        if (!sort.active || sort.direction === '') {
            this.available_assignments = data;
            return;
        }
        this.available_assignments = data.sort((a, b) => {
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
