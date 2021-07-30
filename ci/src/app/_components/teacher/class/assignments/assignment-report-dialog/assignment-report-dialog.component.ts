import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {ClassesManagementService} from '../../../../../_services';
import {Sort} from '@angular/material/sort';
import {compare} from '../../../../../_helpers/compare.helper';

@Component({
    selector: 'app-assignment-report-dialog',
    templateUrl: 'assignment-report-dialog.component.html',
    styleUrls: ['assignment-report-dialog.component.scss']
})
export class AssignmentReportDialogComponent extends BaseDialogComponent<AssignmentReportDialogComponent> implements OnInit {

    public title = 'Assignment Report';
    public assignment = {
        id: 0,
    };
    public students = [];

    constructor(
        public dialogRef: MatDialogRef<AssignmentReportDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.title) {
            this.title = this.data.title;
        }
        if (this.data.assignment) {
            this.assignment = this.data.assignment;
        }
        if (this.data.students) {
            this.students = this.data.students;
        }
        this.resizeDialog();
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
