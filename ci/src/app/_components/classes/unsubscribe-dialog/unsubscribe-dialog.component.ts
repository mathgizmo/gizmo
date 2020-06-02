import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../home/topic/lesson/dialog/base-dialog.component';

@Component({
    selector: 'unsubscribe-dialog',
    templateUrl: 'unsubscribe-dialog.component.html',
    styleUrls: ['unsubscribe-dialog.component.scss']
})
export class UnsubscribeDialogComponent extends BaseDialogComponent<UnsubscribeDialogComponent> {

    constructor(
        public dialogRef: MatDialogRef<UnsubscribeDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '80vw' : '35vw';
        const height = (this.orientation === 'portrait') ? '18vh' : '24vh';
        this.updateDialogSize(width, height);
    }
}
