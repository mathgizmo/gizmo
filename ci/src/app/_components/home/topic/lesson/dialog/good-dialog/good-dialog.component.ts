import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material';

import {BaseDialogComponent} from '../base-dialog.component';

@Component({
    selector: 'good-dialog',
    templateUrl: 'good-dialog.component.html',
    styleUrls: ['good-dialog.component.scss']
})
export class GoodDialogComponent extends BaseDialogComponent<GoodDialogComponent> {
    explanation: string;
    showExplanation = false;

    constructor(
        public dialogRef: MatDialogRef<GoodDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        this.explanation = data.explanation;
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '80vw' : '35vw';
        const height = (this.orientation === 'portrait') ? '29vh' : '32.5vh';
        this.updateDialogSize(width, height);
    }

    showExplanationOnClick() {
        this.showExplanation = !this.showExplanation;
        setTimeout(function () {
            MathJax.Hub.Queue(['Typeset', MathJax.Hub]);
        }, 100);
    }

}
