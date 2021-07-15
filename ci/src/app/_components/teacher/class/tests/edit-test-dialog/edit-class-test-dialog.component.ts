import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';

@Component({
    selector: 'app-edit-class-test-dialog',
    templateUrl: 'edit-class-test-dialog.component.html',
    styleUrls: ['edit-class-test-dialog.component.scss'],
})
export class EditClassTestDialogComponent extends BaseDialogComponent<EditClassTestDialogComponent> implements OnInit {

    title = 'Edit Test';
    test = {
        id: null,
        name: '',
        icon: null,
        start_date: null,
        start_time: null,
        due_date: null,
        due_time: null,
        duration: 0,
        attempts: 1,
        password: '',
        color: '#7FA5C1',
        delete: false
    };
    available_tests = [];
    showDeleteButton = false;

    constructor(
        public dialogRef: MatDialogRef<EditClassTestDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.title) {
            this.title = this.data.title;
        }
        if (this.data.test) {
            this.test = this.data.test;
            if (this.test.id) {
                this.showDeleteButton = true;
            }
        }
        if (this.data.available_tests) {
            this.available_tests = [...this.data.available_tests];
            if (this.test.id) {
                this.available_tests.push(this.test);
            }
            if (this.available_tests.length > 0 && !this.test.id) {
                const first = this.available_tests[0];
                this.test.id = first.id;
                this.test.name = first.name;
                this.test.icon = first.icon;
            }
        }
        this.resizeDialog();
    }

    onSave() {
        const app = this.available_tests.filter(x => +x.id === +this.test.id);
        if (app.length > 0) {
            this.test.name = app[0].name;
            this.test.icon = app[0].icon;
        }
        this.dialogRef.close(this.test);
    }

    onDelete() {
        this.test.delete = true;
        this.dialogRef.close(this.test);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '60vw';
        this.dialogRef.updateSize(width);
    }

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        /* if (event.key === 'Enter') {
            this.dialogRef.close();
        } */
    }

}
