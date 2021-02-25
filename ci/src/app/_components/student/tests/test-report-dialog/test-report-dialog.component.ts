import {Component, HostListener, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';
import {AuthenticationService, ClassesManagementService} from '../../../../_services';
import {User} from '../../../../_models';

@Component({
    selector: 'app-test-report-dialog',
    templateUrl: 'test-report-dialog.component.html',
    styleUrls: ['test-report-dialog.component.scss'],
    providers: [ClassesManagementService, AuthenticationService],
})
export class TestReportDialogComponent extends BaseDialogComponent<TestReportDialogComponent> {
    public test = {
        class: {
            id: 0
        },
        id: 0,
        name: '',
        mark: null,
        questions_count: null,
        attempt_no: 1,
        attempt_id: 0,
        details: []
    };
    private user: User;

    constructor(
        public dialogRef: MatDialogRef<TestReportDialogComponent>,
        private authenticationService: AuthenticationService,
        private classService: ClassesManagementService,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.test) {
            this.test = data.test;
            console.log(this.test);
        }
        this.user = this.authenticationService.userValue;
        this.classService.getTestDetails(this.test.class.id, this.test.id, this.user.user_id, this.test.attempt_id)
            .subscribe(response => {
                this.test.details = response.data;
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
