import {Component, HostListener, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../dialogs/base-dialog.component';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';

@Component({
    selector: 'app-edit-thread-dialog',
    templateUrl: 'edit-thread-dialog.component.html',
    styleUrls: ['edit-thread-dialog.component.scss'],
})
export class EditThreadDialogComponent extends BaseDialogComponent<EditThreadDialogComponent> {

    public thread = {
        'title': '',
        'message': '',
    };
    public title = 'Edit Thread';
    public withTitle = true;

    public editor = ClassicEditor;

    constructor(
        public dialogRef: MatDialogRef<EditThreadDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.thread) {
            // tslint:disable-next-line:indent
        	this.thread = data.thread;
        }
        if (data.hide_title) {
            // tslint:disable-next-line:indent
            this.withTitle = false;
        }
        if (data.title) {
            // tslint:disable-next-line:indent
        	this.title = data.title;
        }
    }

    onSave() {
        this.dialogRef.close(this.thread);
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
