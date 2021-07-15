import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../../../dialogs/base-dialog.component';

@Component({
    selector: 'app-test-report-reset-attempt-dialog',
    templateUrl: 'test-report-reset-attempt-dialog.component.html',
    styleUrls: ['test-report-reset-attempt-dialog.component.scss']
})
export class TestReportResetAttemptDialogComponent extends BaseDialogComponent<TestReportResetAttemptDialogComponent> implements OnInit {

    public item = {
        email: '',
        attempts: []
    };
    public attemptId = 0;

    constructor(
        public dialogRef: MatDialogRef<TestReportResetAttemptDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.item) {
            this.item = this.data.item;
            if (this.item.attempts && this.item.attempts[0].id) {
                this.attemptId = this.item.attempts[0].id;
            }
        }
        this.resizeDialog();
    }

    isNotCompleted() {
        if (!this.attemptId) { return false; }
        const attempt = this.item.attempts.find(o => +o.id === +this.attemptId);
        return attempt && !(attempt.end_at || attempt.mark);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '80vw' : '38vw';
        const height = (this.orientation === 'portrait') ? '30vh' : '38vh';
        this.updateDialogSize(width, height);
    }

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        /* if (event.key === 'Enter') {
            this.dialogRef.close();
        } */
    }
}
