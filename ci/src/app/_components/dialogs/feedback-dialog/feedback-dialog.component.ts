import {Component, OnInit, OnDestroy, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../base-dialog.component';

@Component({
    selector: 'feedback-dialog',
    templateUrl: 'feedback-dialog.component.html',
    styleUrls: ['feedback-dialog.component.scss']
})
export class FeedbackDialogComponent extends BaseDialogComponent<FeedbackDialogComponent> {
    feedback: string;
    question_id: number;

    constructor(
        public dialogRef: MatDialogRef<FeedbackDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        this.feedback = '';
        this.question_id = data.question_id;
    }

    keyClick(event) {
        if (event.key === 'Enter') {
            this.dialogRef.close({text: this.feedback, question_id: this.question_id});
        }
    }

    resizeDialog() {
        let width = (this.orientation === 'portrait') ? '57vw' : '21.5vw';
        let height = (this.orientation === 'portrait') ? '24.5vh' : '26.5vh';
        if (window.innerHeight < 650 && this.orientation === 'landscape') {
            height = '45vh';
            width = '35vw';
        }
        this.updateDialogSize(width, height);
    }

}
