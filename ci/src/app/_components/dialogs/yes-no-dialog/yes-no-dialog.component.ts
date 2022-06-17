import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../base-dialog.component';

@Component({
    selector: 'app-yes-no-dialog',
    templateUrl: 'yes-no-dialog.component.html',
    styleUrls: ['yes-no-dialog.component.scss']
})
export class YesNoDialogComponent extends BaseDialogComponent<YesNoDialogComponent> {

    constructor(
        public dialogRef: MatDialogRef<YesNoDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);

        if (!data.text_yes) {
            data.text_yes = 'yes';
        }

        if (!data.text_no) {
            data.text_no = 'no';
        }
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '80vw' : '35vw';
        // const height = (this.orientation === 'portrait') ? '18vh' : '24vh';
        this.updateDialogSize(width, null);
    }
}
