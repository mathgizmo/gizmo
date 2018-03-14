import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
    selector: 'bad-dialog',
    templateUrl: 'bad-dialog.component.html',
    styleUrls: ['bad-dialog.component.scss']
})
export class BadDialogComponent {
    answers: string[];
    explanation: string;
    showAnswer: boolean;

    constructor(
        public dialogRef: MatDialogRef<BadDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        this.answers = data.data;
        this.explanation = data.explanation;
        this.showAnswer = data.showAnswers;
        setTimeout(function() {
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        }, 50);
    }

    onNoClick(): void {
        this.dialogRef.close();
    }
}