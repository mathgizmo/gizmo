import {Component, HostListener, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {AuthenticationService, UserService} from '../../../../../_services';
import {MatSnackBar} from '@angular/material/snack-bar';
import {User} from '../../../../../_models';

@Component({
    selector: 'app-test-options-dialog',
    templateUrl: 'test-options-dialog.component.html',
    styleUrls: ['test-options-dialog.component.scss'],
    providers: [UserService, AuthenticationService],
})
export class TestOptionsDialogComponent extends BaseDialogComponent<TestOptionsDialogComponent> {

    user: User;
    title = 'Edit Test Options';

    constructor(
        private authenticationService: AuthenticationService,
        private userService: UserService,
        public snackBar: MatSnackBar,
        public dialogRef: MatDialogRef<TestOptionsDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        this.user = this.authenticationService.userValue;
    }

    onSave() {
        this.userService.changeOptions(this.user.options).subscribe(res => {
            this.authenticationService.saveUserValue(this.user);
            this.snackBar.open('Test options have been successfully updated!', '', {
                duration: 3000,
                panelClass: ['success-snackbar']
            });
            this.dialogRef.close(true);
        }, error => {
            let message = '';
            if (typeof error === 'object') {
                Object.values(error).forEach(x => {
                    message += x + ' ';
                });
            } else {
                message = error;
            }
            this.snackBar.open(message ? message : 'Error occurred while updating test options!', '', {
                duration: 3000,
                panelClass: ['error-snackbar']
            });
            this.dialogRef.close(true);
        });
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '60vw';
        this.dialogRef.updateSize(width);
    }

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        /* if (event.key === 'Enter') {
            this.dialogRef.close();
        } */
    }

}
