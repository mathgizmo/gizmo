import {Component, HostListener, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';

@Component({
    selector: 'app-email-teacher-dialog',
    templateUrl: 'email-teacher-dialog.component.html',
    styleUrls: ['email-teacher-dialog.component.scss'],
})
export class EmailTeacherDialogComponent extends BaseDialogComponent<EmailTeacherDialogComponent> {

    public class = {
        id: 0,
        name: '',
    };
    public mail = {
        subject: '',
        body: '',
    };

    public editor = ClassicEditor;

    constructor(
        public dialogRef: MatDialogRef<EmailTeacherDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.class) {
            // tslint:disable-next-line:indent
        	this.class = data.class;
        }
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '80vw';
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
