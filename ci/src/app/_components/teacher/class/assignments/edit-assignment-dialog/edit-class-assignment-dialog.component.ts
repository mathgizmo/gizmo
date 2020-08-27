import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {DomSanitizer} from '@angular/platform-browser';

@Component({
    selector: 'app-edit-class-assignment-dialog',
    templateUrl: 'edit-class-assignment-dialog.component.html',
    styleUrls: ['edit-class-assignment-dialog.component.scss'],
})
export class EditClassAssignmentDialogComponent extends BaseDialogComponent<EditClassAssignmentDialogComponent> {

    title = 'Edit Assignment';
    assignment = {
        id: null,
        name: null,
        icon: null,
        start_date: null,
        start_time: null,
        due_date: null,
        due_time: null,
        color: '#7FA5C1'
    };
    available_assignments = [];

    constructor(
        private sanitizer: DomSanitizer,
        public dialogRef: MatDialogRef<EditClassAssignmentDialogComponent>,
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
        if (data.available_assignments) {
            // tslint:disable-next-line:indent
            this.available_assignments = [...data.available_assignments];
            if (this.assignment.id) {
                this.available_assignments.push(this.assignment);
            }
            if (this.available_assignments.length > 0) {
                const first = this.available_assignments[0];
                this.assignment.id = first.id;
                this.assignment.name = first.name;
                this.assignment.icon = first.icon;
            }
        }
    }

    onSave() {
        const app = this.available_assignments.filter(x => +x.id === +this.assignment.id);
        if (app.length > 0) {
            this.assignment.name = app[0].name;
            this.assignment.icon = app[0].icon;
        }
        this.dialogRef.close(this.assignment);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '60vw';
        this.dialogRef.updateSize(width);
    }

}
