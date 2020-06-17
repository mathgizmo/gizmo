import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA, MatDialog} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../home/topic/lesson/dialog/base-dialog.component';
import {Sort} from '@angular/material/sort';
import {StudentAssignmentsDialogComponent} from './student-assignments-dialog/student-assignments-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';

@Component({
    selector: 'class-students-dialog',
    templateUrl: 'class-students-dialog.component.html',
    styleUrls: ['class-students-dialog.component.scss'],
})
export class ClassStudentsDialogComponent extends BaseDialogComponent<ClassStudentsDialogComponent> {

    students = [];
    class: any;

    public id: number;
    public name: string;
    public first_name: string;
    public last_name: string;
    public email: string;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(
        public dialog: MatDialog,
        private deviceService: DeviceDetectorService,
        public dialogRef: MatDialogRef<ClassStudentsDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.class) {
            // tslint:disable-next-line:indent
        	this.class = data.class;
        }
        if (data.students) {
            // tslint:disable-next-line:indent
        	this.students = data.students;
        }
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '80vw';
        // const height = (this.orientation === 'portrait') ? '80vh' : '75vh';
        // this.updateDialogSize(width, height);
        this.dialogRef.updateSize(width);
    }

    sortData(sort: Sort) {
        const data = this.students.slice();
        if (!sort.active || sort.direction === '') {
            this.students = data;
            return;
        }
        this.students = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'first_name': return compare(a.first_name, b.first_name, isAsc);
                case 'last_name': return compare(a.last_name, b.last_name, isAsc);
                case 'email': return compare(a.email, b.email, isAsc);
                case  'assignments_finished_count': return compare(a.assignments_finished_count, b.assignments_finished_count, isAsc);
                case  'assignments_past_due_count': return compare(a.assignments_past_due_count, b.assignments_past_due_count, isAsc);
                default: return 0;
            }
        });
    }

    showAssignments(student) {
        const dialogRef = this.dialog.open(StudentAssignmentsDialogComponent, {
            data: { 'assignments': student.assignments, 'student': student},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {});
    }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
