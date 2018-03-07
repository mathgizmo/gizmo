import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';


@Component({
    selector: 'report-dialog',
    template: `<h2 mat-dialog-title>Please specify reason</h2>
        <mat-dialog-content>
            <mat-radio-group class="radio-group" [(ngModel)]="selectedOption">
              <mat-radio-button class="radio-button" *ngFor="let option of options; let optionIndex = index" [value]="optionIndex">
                {{option}}
              </mat-radio-button>
            </mat-radio-group>
        </mat-dialog-content>
        <mat-form-field *ngIf="selectedOption == 3">
            <input matInput [(ngModel)]="custom">
        </mat-form-field>
        <mat-dialog-actions>
            <button mat-button [mat-dialog-close]="{option: options[selectedOption], text: custom, question_id: question_id, answers: answers}" style="background-color: #31698a">Send</button>
            <button mat-button [mat-dialog-close]="false" style="background-color: #6dc066">Cancel</button>
        </mat-dialog-actions>`
})
export class ReportDialogComponent {
    custom: string;
    selectedOption: number;
    answers: string[];
    question_id: number;

    options = [
        'Wording of question is confusing or unclear',
        'Answer is incorrect',
        'question does not belong in this topic',
        'other',
      ];

    constructor(
        public dialogRef: MatDialogRef<ReportDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        this.custom = "";
        this.answers = data.answers;
        this.question_id = data.question_id;
    }

    onNoClick(): void {
        this.dialogRef.close();
    }
}