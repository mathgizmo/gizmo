import {Component, OnInit, OnDestroy, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material';

import {BaseDialogComponent} from '../base-dialog.component';

@Component({
    selector: 'bad-dialog',
    templateUrl: 'bad-dialog.component.html',
    styleUrls: ['bad-dialog.component.scss']
})
export class BadDialogComponent extends BaseDialogComponent<BadDialogComponent> {
    answers: string[];
    explanation: string;
    showAnswer: boolean;

    constructor(
        public dialogRef: MatDialogRef<BadDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        this.answers = data.data;
        this.explanation = data.explanation;
        this.showAnswer = data.showAnswers;
        setTimeout(function () {
            MathJax.Hub.Queue(['Typeset', MathJax.Hub]);
        }, 50);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '80vw' : '35vw';
        const height = (this.orientation === 'portrait') ? '30vh' : '32.5vh';
        this.updateDialogSize(width, height);
    }

}
