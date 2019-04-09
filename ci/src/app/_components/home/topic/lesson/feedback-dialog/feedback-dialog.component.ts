import { Component, OnInit, OnDestroy, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';


@Component({
    selector: 'feedback-dialog',
    templateUrl: 'feedback-dialog.component.html',
    styleUrls: ['feedback-dialog.component.scss']
})
export class FeedbackDialogComponent {
    feedback: string;
    question_id: number;

    constructor(
        public dialogRef: MatDialogRef<FeedbackDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        this.feedback = "";
        this.question_id = data.question_id;
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    ngOnInit() {
        this.keyClick = this.keyClick.bind(this);
        document.addEventListener('keyup', this.keyClick);
    }    

    ngOnDestroy() {
        document.removeEventListener('keyup', this.keyClick);
    }

    keyClick(event) {
        if(event.key === "Enter") {
          this.dialogRef.close({text: this.feedback, question_id: this.question_id});
        }
    }

}