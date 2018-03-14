import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';


@Component({
    selector: 'report-dialog',
    templateUrl: 'report-dialog.component.html',
    styleUrls: ['report-dialog.component.scss']
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