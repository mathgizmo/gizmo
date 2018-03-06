import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
    selector: 'bad-dialog',
    template: `<h2 mat-dialog-title>Incorrect :(</h2>
        <mat-dialog-content>
            <div *ngIf="(answers.length == 1) && showAnswer">
                Correct answer is: {{answers[0].value}}
            </div>
            <div *ngIf="(answers.length != 1) && showAnswer">
                Correct answers are: 
                <ul>
                    <li *ngFor="let answer of answers; let answerIndex = index">
                        {{answer.value}}
                    </li>
                </ul>
            </div>
            <div *ngIf="explanation!=''">
                {{explanation}}
            </div>
        </mat-dialog-content>
        <mat-dialog-actions>
            <button mat-button [mat-dialog-close]="false" style="background-color: #fef65b">Continue</button>
            <button mat-button [mat-dialog-close]="true" style="background-color: #ff4444">Report Error!</button>
        </mat-dialog-actions>`,
    styles: [`
        div { min-height: 40px; }
    `]
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
        //this.answers[0]['value'] = "$$E=mc^2$$"; // test equation in LaTeX
        setTimeout(function() {
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        }, 50);
    }

    onNoClick(): void {
        this.dialogRef.close();
    }
}