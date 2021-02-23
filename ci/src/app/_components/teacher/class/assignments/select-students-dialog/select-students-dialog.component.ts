import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {DomSanitizer} from '@angular/platform-browser';
import {Sort} from '@angular/material/sort';

@Component({
    selector: 'app-select-students-dialog',
    templateUrl: 'select-students-dialog.component.html',
    styleUrls: ['select-students-dialog.component.scss'],
})
export class SelectStudentsDialogComponent extends BaseDialogComponent<SelectStudentsDialogComponent> {

    title = 'Select Students';
    students = [];
    selected_students = [];
    public email: string;

    constructor(
        private sanitizer: DomSanitizer,
        public dialogRef: MatDialogRef<SelectStudentsDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.title) {
            // tslint:disable-next-line:indent
            this.title = data.title;
        }
        if (data.students) {
            // tslint:disable-next-line:indent
            this.students = data.students;
        }
        if (data.selected_students) {
            // tslint:disable-next-line:indent
            this.selected_students = data.selected_students;
        }
    }

    onSave() {
        this.dialogRef.close(this.selected_students);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '60vw';
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
                case 'name': return compare(a.first_name + ' ' + a.last_name, b.first_name + ' ' + b.last_name, isAsc);
                case 'first_name': return compare(a.first_name, b.first_name, isAsc);
                case 'last_name': return compare(a.last_name, b.last_name, isAsc);
                case 'email': return compare(a.email, b.email, isAsc);
                default: return 0;
            }
        });
    }

    isAllSelected() {
        return this.students.length === this.selected_students.length;
    }

    masterToggle() {
        this.isAllSelected() ? this.selected_students = []
            : this.selected_students = this.students.map(s => s.id);
    }

    toggleStudentChecked(student) {
        if (this.isStudentChecked(student)) {
            this.selected_students = this.selected_students.filter(s => s !== student.id);
        } else {
            this.selected_students.push(student.id);
        }
    }

    isStudentChecked(student) {
        return this.selected_students.filter(s => s === student.id).length > 0;
    }

}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    if (typeof a === 'string' || typeof b === 'string') {
        a = ('' + a).toLowerCase();
        b = ('' + b).toLowerCase();
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
