import { OnInit, OnDestroy, Inject, HostListener } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';

export abstract class BaseDialogComponent<T> {

  protected orientation = (window.innerHeight > window.innerWidth) ? 'portrait' : 'landscape';

  protected constructor(public dialogRef: MatDialogRef<T>, @Inject(MAT_DIALOG_DATA) public data: any) {
  }

  @HostListener('window:resize', ['$event'])
  onResize(event) {
    let newOrientation = (window.innerHeight > window.innerWidth) ? 'portrait' : 'landscape';
    if(newOrientation != this.orientation) {
      this.orientation = newOrientation;
      this.resizeDialog();
    }
  }

  protected abstract resizeDialog() : void;

  protected updateDialogSize(width: string, height: string) {
    this.dialogRef.updateSize(width, height);
  }

  protected ngOnInit() {
    this.keyClick = this.keyClick.bind(this);
    document.addEventListener('keyup', this.keyClick);
    this.resizeDialog();
  }

  protected ngOnDestroy() {
    document.removeEventListener('keyup', this.keyClick);
  }

  protected keyClick(event) {
    if(event.key === "Enter") {
      this.dialogRef.close();
    }
  }

  protected onNoClick(): void {
    this.dialogRef.close();
  }

}