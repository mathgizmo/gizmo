import {OnInit, OnDestroy, Inject, HostListener} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material';

export abstract class BaseDialogComponent<T> implements OnInit, OnDestroy {

    protected orientation = (window.innerHeight > window.innerWidth) ? 'portrait' : 'landscape';

    protected constructor(public dialogRef: MatDialogRef<T>, @Inject(MAT_DIALOG_DATA) public data: any) {
    }

    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        if (event.key === 'Enter') {
            this.dialogRef.close();
        }
    }

    @HostListener('window:resize', ['$event'])
    onResize(event) {
        const newOrientation = (window.innerHeight > window.innerWidth) ? 'portrait' : 'landscape';
        if (newOrientation !== this.orientation) {
            this.orientation = newOrientation;
            this.resizeDialog();
        }
    }

    protected abstract resizeDialog(): void;

    protected updateDialogSize(width: string, height: string) {
        this.dialogRef.updateSize(width, height);
    }

    public ngOnInit() {
        this.resizeDialog();
    }

    public ngOnDestroy() {
    }

    protected onNoClick(): void {
        this.dialogRef.close();
    }

}
