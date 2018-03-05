import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
    selector: 'good-dialog',
    template: `<h2 mat-dialog-title>Correct!</h2>
        <mat-dialog-content></mat-dialog-content>
        <mat-dialog-actions>
          <button mat-button [mat-dialog-close]="true" style="background-color: #fef65b">Continue</button>
        </mat-dialog-actions>`
})
export class GoodDialogComponent {
    constructor(
        public dialogRef: MatDialogRef<GoodDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {

    }

    onNoClick(): void {
        this.dialogRef.close();
    }
}