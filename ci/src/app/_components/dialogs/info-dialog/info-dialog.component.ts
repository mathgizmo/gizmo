import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../base-dialog.component';

@Component({
    selector: 'app-info-dialog',
    templateUrl: 'info-dialog.component.html',
    styleUrls: ['info-dialog.component.scss']
})
export class InfoDialogComponent extends BaseDialogComponent<InfoDialogComponent> {

    constructor(
        public dialogRef: MatDialogRef<InfoDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '80vw' : '35vw';
        // const height = (this.orientation === 'portrait') ? '28vh' : '30vh';
        this.updateDialogSize(width, null);
    }
}
