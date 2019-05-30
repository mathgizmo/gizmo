import { Component, OnInit, OnDestroy, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

import { BaseDialogComponent } from '../base-dialog.component';


@Component({
    selector: 'report-dialog',
    templateUrl: 'report-dialog.component.html',
    styleUrls: ['report-dialog.component.scss']
})
export class ReportDialogComponent extends BaseDialogComponent<ReportDialogComponent> {
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
        super(dialogRef, data);
        this.custom = '';
        this.answers = data.answers;
        this.question_id = data.question_id;
    }

    keyClick(event) {
        if (event.key === 'Enter') {
            if (this.selectedOption) {
                this.dialogRef.close({
                  option: this.options[this.selectedOption],
                  text: this.custom,
                  question_id: this.question_id,
                  answers: this.answers
              });
            }
        }
    }

    resizeDialog() {
        let width =  (this.orientation === 'portrait') ? '78vw' : '30vw';
        let height = (this.orientation === 'portrait') ? '30vh' : '32vh';
        if (window.innerHeight < 650 && this.orientation === 'landscape') {
            height = '45vh';
            width = '35vw';
        }
        this.updateDialogSize(width, height);
    }
}
