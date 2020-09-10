import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../base-dialog.component';

@Component({
    selector: 'delete-confirmation-dialog',
    templateUrl: 'delete-confirmation-dialog.component.html',
    styleUrls: ['delete-confirmation-dialog.component.scss']
})
export class DeleteConfirmationDialogComponent extends BaseDialogComponent<DeleteConfirmationDialogComponent> {

    constructor(
        public dialogRef: MatDialogRef<DeleteConfirmationDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '80vw' : '35vw';
        const height = (this.orientation === 'portrait') ? '18vh' : '30vh';
        this.updateDialogSize(width, height);
    }
}
