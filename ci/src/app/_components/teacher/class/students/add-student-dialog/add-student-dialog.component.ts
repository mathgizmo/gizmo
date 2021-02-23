import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';

@Component({
    selector: 'app-add-student-dialog',
    templateUrl: 'add-student-dialog.component.html',
    styleUrls: ['add-student-dialog.component.scss'],
})
export class AddStudentDialogComponent extends BaseDialogComponent<AddStudentDialogComponent> {

    public email = '';
    public file: any;

    constructor(
        public dialogRef: MatDialogRef<AddStudentDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    fileChanged(e) {
        this.file = e.target.files[0];
        if (this.file) {
            const fileReader = new FileReader();
            fileReader.onload = (e) => {
                this.email = fileReader.result.toString();
            };
            fileReader.readAsText(this.file);
        }
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '50vw';
        this.dialogRef.updateSize(width);
    }
}
