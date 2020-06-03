import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../home/topic/lesson/dialog/base-dialog.component';

@Component({
    selector: 'edit-class-dialog',
    templateUrl: 'edit-class-dialog.component.html',
    styleUrls: ['edit-class-dialog.component.scss'],
})
export class EditClassDialogComponent extends BaseDialogComponent<EditClassDialogComponent> {

    class = {
        'name': '',
        'subscription_type': 'open',
        'invitations': ''
    };
    title = 'Edit Class';

    constructor(
        public dialogRef: MatDialogRef<EditClassDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.class) {
            // tslint:disable-next-line:indent
        	this.class = data.class;
        }
        if (data.title) {
            // tslint:disable-next-line:indent
        	this.title = data.title;
        }
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '50vw';
        this.dialogRef.updateSize(width);
    }
}
