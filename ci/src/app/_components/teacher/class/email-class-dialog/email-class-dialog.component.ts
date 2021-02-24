import {Component, HostListener, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';
import {ClassesManagementService} from '../../../../_services';

@Component({
    selector: 'app-email-class-dialog',
    templateUrl: 'email-class-dialog.component.html',
    styleUrls: ['email-class-dialog.component.scss'],
    providers: [ClassesManagementService]
})
export class EmailClassDialogComponent extends BaseDialogComponent<EmailClassDialogComponent> {

    public class = {
        id: 0,
        name: '',
    };
    public students = [];
    public mail = {
        class_id: 0,
        subject: '',
        body: '',
        students: [],
        for_all_students: true
    };

    public editor = ClassicEditor;

    constructor(
        private classService: ClassesManagementService,
        public dialogRef: MatDialogRef<EmailClassDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.class) {
            // tslint:disable-next-line:indent
        	this.class = data.class;
        }
        this.classService.getStudents(this.class.id)
            .subscribe(students => {
                this.students = students;
            });
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '80vw';
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
