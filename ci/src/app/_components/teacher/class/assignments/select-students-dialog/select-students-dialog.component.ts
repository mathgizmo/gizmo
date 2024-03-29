import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {Sort} from '@angular/material/sort';
import {compare} from '../../../../../_helpers/compare.helper';

@Component({
    selector: 'app-select-students-dialog',
    templateUrl: 'select-students-dialog.component.html',
    styleUrls: ['select-students-dialog.component.scss'],
})
export class SelectStudentsDialogComponent extends BaseDialogComponent<SelectStudentsDialogComponent> implements OnInit {

    title = 'Select Students';
    students = [];
    selected_students = [];
    public email: string;

    constructor(
        public dialogRef: MatDialogRef<SelectStudentsDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.title) {
            this.title = this.data.title;
        }
        if (this.data.students) {
            this.students = this.data.students;
        }
        if (this.data.selected_students) {
            this.selected_students = this.data.selected_students;
        }
        this.resizeDialog();
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

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        /* if (event.key === 'Enter') {
            this.dialogRef.close();
        } */
    }

}
