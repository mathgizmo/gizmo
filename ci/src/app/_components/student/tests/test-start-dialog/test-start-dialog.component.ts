import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';
import {AuthenticationService, UserService} from '../../../../_services';
import {MatSnackBar} from '@angular/material/snack-bar';
import {User} from '../../../../_models';

@Component({
    selector: 'app-test-start-dialog',
    templateUrl: 'test-start-dialog.component.html',
    styleUrls: ['test-start-dialog.component.scss'],
    providers: [UserService, AuthenticationService],
})
export class TestStartDialogComponent extends BaseDialogComponent<TestStartDialogComponent> {

    title = 'Start the test';
    test: {
        class_app_id: 0,
        name: '',
        duration: 0,
        total_questions_count: 0
    };

    constructor(
        private authenticationService: AuthenticationService,
        private userService: UserService,
        public snackBar: MatSnackBar,
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
