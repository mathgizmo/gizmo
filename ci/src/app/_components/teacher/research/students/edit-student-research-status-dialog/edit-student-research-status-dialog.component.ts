import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';

@Component({
    selector: 'app-edit-student-research-status-dialog',
    templateUrl: 'edit-student-research-status-dialog.component.html',
    styleUrls: ['edit-student-research-status-dialog.component.scss'],
})
export class EditStudentResearchStatusDialogComponent
    extends BaseDialogComponent<EditStudentResearchStatusDialogComponent>
    implements OnInit {

    public student = {
        id: 0,
        email: '',
        pivot: {
            is_consent_read: 0,
            is_element1_accepted: 0,
            is_element2_accepted: 0,
            is_element3_accepted: 0,
            is_element4_accepted: 0
        }
    };

    constructor(
        public dialogRef: MatDialogRef<EditStudentResearchStatusDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.student) {
            this.student = this.data.student;
        }
        this.resizeDialog();
    }

    onSave() {
        this.dialogRef.close(this.student.pivot);
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
