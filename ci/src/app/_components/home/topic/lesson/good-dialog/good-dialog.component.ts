import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

@Component({
    selector: 'good-dialog',
    templateUrl: 'good-dialog.component.html',
    styleUrls: ['good-dialog.component.scss']
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