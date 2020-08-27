import {Component, OnInit, ViewChild} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {MatDialog} from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import * as moment from 'moment';

import {ClassesManagementService} from '../../../../_services';
import {YesNoDialogComponent} from '../../../dialogs/index';
import {DeviceDetectorService} from 'ngx-device-detector';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {ActivatedRoute} from '@angular/router';
import {EditClassAssignmentDialogComponent} from './edit-assignment-dialog/edit-class-assignment-dialog.component';
import {ClassAssignmentsCalendarComponent} from './calendar/class-assignments-calendar.component';

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
        name: ''
    };
    addAssignment = false;
    nameFilter;

    currentDate = (new Date()).toISOString().split('T')[0];

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
                    this.class = classes.filter(x => x.id === this.classId)[0];
                    this.available_assignments = res['available_assignments'];
                    this.assignments = res['assignments'];
                    this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Assignments';
                    this.updateStatuses();
                });
        });
    }

    onAssignmentDateChanged(event) {
        const items = this.assignments.filter(x => x.id === +event.id);
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
                    this.updateStatuses();
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
                                return x.id !== app.id;
                            });
                            this.updateStatuses();
                            this.calendarComponent.updateCalendarEvents();
                            this.snackBar.open('Assignment have been successfully added!', '', {
                                duration: 3000,
                                panelClass: ['success-snackbar']
                            });
                        }, error => {
                            this.snackBar.open('Error occurred while adding assignment!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                    }, error => {
                        this.snackBar.open('Error occurred while adding assignment!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onAssignmentEditClicked(appId) {
        const item = this.assignments.filter(x => x.id === appId)[0];
        const dialogRef = this.dialog.open(EditClassAssignmentDialogComponent, {
            data: { 'title': 'Edit Assignment', 'assignment': item, 'available_assignments': this.available_assignments },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(app => {
            if (app) {
                this.classService.changeAssignment(this.classId, app)
                    .subscribe(response => {
                        this.updateStatuses();
                        this.calendarComponent.updateCalendarEvents();
                        this.snackBar.open('Assignment have been successfully updated!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                    }, error => {
                        this.snackBar.open('Error occurred while updating assignment!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
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

    onAddAssignment(app) {
        this.classService.addAssignmentToClass(this.classId, app.id)
            .subscribe(response => {
                this.assignments.unshift(app);
                this.available_assignments  = this.available_assignments.filter( (item) => {
                    return item.id !== app.id;
                });
                this.addAssignment = !this.addAssignment;
                this.updateStatuses();
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
                this.updateStatuses();
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
                this.updateStatuses();
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
                this.updateStatuses();
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
            item.start_date = this.currentDate;
        }
        this.classService.changeAssignment(this.classId, item)
            .subscribe(assignments => {
                this.snackBar.open('Start Time Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
                this.updateStatuses();
            }, error => {
                this.snackBar.open('Error occurred while saving Start Time!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onDeleteAssignment(item) {
        const dialogRef = this.dialog.open(YesNoDialogComponent, {
            data: { 'message': 'Are you sure that you want to delete this assignments from the class?'},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.deleteAssignmentFromClass(this.classId, item.id)
                    .subscribe(response => {
                        this.available_assignments.unshift(item);
                        this.assignments  = this.assignments.filter( (x) => {
                            return x.id !== item.id;
                        });
                    });
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

function compare(a: number | string, b: number | string, isAsc: boolean) {
    if (typeof a === 'string' || typeof b === 'string') {
        a = ('' + a).toLowerCase();
        b = ('' + b).toLowerCase();
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
