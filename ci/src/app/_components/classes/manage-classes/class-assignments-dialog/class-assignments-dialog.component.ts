import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA, MatDialog} from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';

import {BaseDialogComponent} from '../../../home/topic/lesson/dialog/base-dialog.component';
import {Sort} from '@angular/material/sort';
import {ClassesManagementService} from '../../../../_services';
import {YesNoDialogComponent} from '../../yes-no-dialog/yes-no-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';

@Component({
    selector: 'class-assignments-dialog',
    templateUrl: 'class-assignments-dialog.component.html',
    styleUrls: ['class-assignments-dialog.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassAssignmentsDialogComponent extends BaseDialogComponent<ClassAssignmentsDialogComponent> {

    assignments = [];
    available_assignments = [];
    class: any;
    addAssignment = false;
    nameFilter;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    private readonly adminUrl = environment.adminUrl;

    constructor(
        public snackBar: MatSnackBar,
        private classService: ClassesManagementService,
        public dialog: MatDialog, private deviceService: DeviceDetectorService,
        private sanitizer: DomSanitizer,
        public dialogRef: MatDialogRef<ClassAssignmentsDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.class) {
            // tslint:disable-next-line:indent
        	this.class = data.class;
        }
        if (data.assignments) {
            // tslint:disable-next-line:indent
        	this.assignments = data.assignments;
        }
        if (data.available_assignments) {
            // tslint:disable-next-line:indent
            this.available_assignments = data.available_assignments;
        }
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    onAddAssignment(app) {
        this.classService.addAssignmentToClass(this.class.id, app.id)
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
        this.classService.changeAssignment(this.class.id, item)
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

    onStartChanged(item, newStart) {
        item.start_at = newStart;
        this.classService.changeAssignment(this.class.id, item)
            .subscribe(assignments => {
                this.snackBar.open('Start Date & Time Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving Start Date & Time!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onTimeToDueDateChanged(item, isTimeToDueDate) {
        item.time_to_due_date = isTimeToDueDate;
        this.classService.changeAssignment(this.class.id, item)
            .subscribe(assignments => {
                this.snackBar.open('Time to Due Date Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving Time to Due Date!', '', {
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
                this.classService.deleteAssignmentFromClass(this.class.id, item.id)
                    .subscribe(response => {
                        this.available_assignments.unshift(item);
                        this.assignments  = this.assignments.filter( (x) => {
                            return x.id !== item.id;
                        });
                    });
            }
        });
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '80vw';
        // const height = (this.orientation === 'portrait') ? '80vh' : '75vh';
        // this.updateDialogSize(width, height);
        this.dialogRef.updateSize(width);
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
                case 'due_date': return compare(a.due_date, b.due_date, isAsc);
                case 'start_at': return compare(a.start_at, b.start_at, isAsc);
                case 'time_to_due_date': return compare(a.time_to_due_date, b.time_to_due_date, isAsc);
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
