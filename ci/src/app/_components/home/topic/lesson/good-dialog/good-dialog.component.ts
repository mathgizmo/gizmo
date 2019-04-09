import { Component, OnInit, OnDestroy, Inject } from '@angular/core';
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

  ngOnInit() {
    this.keyClick = this.keyClick.bind(this);
    document.addEventListener('keyup', this.keyClick);
  }

  ngOnDestroy() {
    document.removeEventListener('keyup', this.keyClick);
  }

  keyClick(event) {
    if(event.key === "Enter") {
      this.dialogRef.close();
    }
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

}