import {Component, OnInit} from '@angular/core';
import {MatDialog} from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';

import {Sort} from '@angular/material/sort';
import {ClassesManagementService} from '../../../../_services';
import {YesNoDialogComponent} from '../../../dialogs/yes-no-dialog/yes-no-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {ActivatedRoute} from '@angular/router';

@Component({
    selector: 'app-class-dashboard',
    templateUrl: './class-dashboard.component.html',
    styleUrls: ['./class-dashboard.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassDashboardComponent implements OnInit {

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

    private sub: any;

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
                });
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
            item.start_date = this.currentDate;
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
