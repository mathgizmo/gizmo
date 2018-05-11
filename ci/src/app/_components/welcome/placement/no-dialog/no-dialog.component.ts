import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
    selector: 'no-dialog',
    templateUrl: 'no-dialog.component.html',
    styleUrls: ['no-dialog.component.scss']
})
export class NoDialogComponent {
    questionNum = localStorage.getItem('question_num');

    constructor(
        public dialogRef: MatDialogRef<NoDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {

    }

    onNoClick(): void {
        this.dialogRef.close();

    }
}