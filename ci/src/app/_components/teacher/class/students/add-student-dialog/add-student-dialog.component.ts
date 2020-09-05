import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';

@Component({
    selector: 'app-add-student-dialog',
    templateUrl: 'add-student-dialog.component.html',
    styleUrls: ['add-student-dialog.component.scss'],
})
export class AddStudentDialogComponent extends BaseDialogComponent<AddStudentDialogComponent> {

    email = '';

    constructor(
        public dialogRef: MatDialogRef<AddStudentDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '50vw';
        this.dialogRef.updateSize(width);
    }
}
