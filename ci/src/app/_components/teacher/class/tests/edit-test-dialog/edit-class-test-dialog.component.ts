import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {DomSanitizer} from '@angular/platform-browser';

@Component({
    selector: 'app-edit-class-test-dialog',
    templateUrl: 'edit-class-test-dialog.component.html',
    styleUrls: ['edit-class-test-dialog.component.scss'],
})
export class EditClassTestDialogComponent extends BaseDialogComponent<EditClassTestDialogComponent> {

    title = 'Edit Test';
    test = {
        id: null,
        name: null,
        icon: null,
        start_date: null,
        start_time: null,
        due_date: null,
        due_time: null,
        duration: null,
        color: '#7FA5C1',
        delete: false
    };
    available_tests = [];
    showDeleteButton = false;

    constructor(
        private sanitizer: DomSanitizer,
        public dialogRef: MatDialogRef<EditClassTestDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.title) {
            // tslint:disable-next-line:indent
            this.title = data.title;
        }
        if (data.test) {
            // tslint:disable-next-line:indent
            this.test = data.test;
            if (this.test.id) {
                this.showDeleteButton = true;
            }
        }
        if (data.available_tests) {
            // tslint:disable-next-line:indent
            this.available_tests = [...data.available_tests];
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

}
