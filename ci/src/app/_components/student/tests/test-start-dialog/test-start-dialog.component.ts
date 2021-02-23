import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';

@Component({
    selector: 'app-test-start-dialog',
    templateUrl: 'test-start-dialog.component.html',
    styleUrls: ['test-start-dialog.component.scss'],
})
export class TestStartDialogComponent extends BaseDialogComponent<TestStartDialogComponent> {

    public test: {
        class_app_id: 0,
        name: '',
        duration: 0,
        total_questions_count: 0,
        is_revealed: true
    };

    public password;

    constructor(
        public dialogRef: MatDialogRef<TestStartDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.test) {
            // tslint:disable-next-line:indent
            this.test = data.test;
        }
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '60vw';
        this.dialogRef.updateSize(width);
    }

}
