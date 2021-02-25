import {Component, HostListener, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {ClassesManagementService} from '../../../../../_services';
import {Sort} from '@angular/material/sort';

@Component({
    selector: 'app-assignment-report-dialog',
    templateUrl: 'assignment-report-dialog.component.html',
    styleUrls: ['assignment-report-dialog.component.scss'],
    providers: [ClassesManagementService]
})
export class AssignmentReportDialogComponent extends BaseDialogComponent<AssignmentReportDialogComponent> {

    public title = 'Assignment Report';
    public assignment = {
        id: 0,
    };
    public students = [];

    constructor(
        public dialogRef: MatDialogRef<AssignmentReportDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.title) {
            // tslint:disable-next-line:indent
            this.title = data.title;
        }
        if (data.assignment) {
            // tslint:disable-next-line:indent
            this.assignment = data.assignment;
        }
        if (data.students) {
            // tslint:disable-next-line:indent
            this.students = data.students;
        }
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '80vw';
        this.dialogRef.updateSize(width);
    }

    sortData(sort: Sort) {
        const data = this.students.slice();
        if (!sort.active || sort.direction === '') {
            this.students = data;
            return;
        }
        const compare = (a: number | string, b: number | string, isAsc: boolean) => {
            if (typeof a === 'string' || typeof b === 'string') {
                a = ('' + a).toLowerCase();
                b = ('' + b).toLowerCase();
            }
            return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
        };
        this.students = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'name': return compare(a.name, b.name, isAsc);
                default: return 0;
            }
        });
    }

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        /* if (event.key === 'Enter') {
            this.dialogRef.close();
        } */
    }
}
