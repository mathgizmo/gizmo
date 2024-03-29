import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';

@Component({
    selector: 'app-edit-class-assignment-dialog',
    templateUrl: 'edit-class-assignment-dialog.component.html',
    styleUrls: ['edit-class-assignment-dialog.component.scss'],
})
export class EditClassAssignmentDialogComponent extends BaseDialogComponent<EditClassAssignmentDialogComponent> implements OnInit {

    title = 'Edit Assignment';
    assignment = {
        id: null,
        name: null,
        icon: null,
        start_date: null,
        start_time: null,
        due_date: null,
        due_time: null,
        color: '#7FA5C1',
        delete: false
    };
    available_assignments = [];
    showDeleteButton = false;

    constructor(
        public dialogRef: MatDialogRef<EditClassAssignmentDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.title) {
            this.title = this.data.title;
        }
        if (this.data.assignment) {
            this.assignment = this.data.assignment;
            if (this.assignment.id) {
                this.showDeleteButton = true;
            }
        }
        if (this.data.available_assignments) {
            this.available_assignments = [...this.data.available_assignments];
            if (this.assignment.id) {
                this.available_assignments.push(this.assignment);
            }
            if (this.available_assignments.length > 0 && !this.assignment.id) {
                const first = this.available_assignments[0];
                this.assignment.id = first.id;
                this.assignment.name = first.name;
                this.assignment.icon = first.icon;
            }
        }
        this.resizeDialog();
    }

    onSave() {
        const app = this.available_assignments.filter(x => +x.id === +this.assignment.id);
        if (app.length > 0) {
            this.assignment.name = app[0].name;
            this.assignment.icon = app[0].icon;
        }
        this.dialogRef.close(this.assignment);
    }

    onDelete() {
        this.assignment.delete = true;
        this.dialogRef.close(this.assignment);
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
