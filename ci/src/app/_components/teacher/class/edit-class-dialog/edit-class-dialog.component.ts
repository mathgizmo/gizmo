import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';

@Component({
    selector: 'app-edit-class-dialog',
    templateUrl: 'edit-class-dialog.component.html',
    styleUrls: ['edit-class-dialog.component.scss'],
})
export class EditClassDialogComponent extends BaseDialogComponent<EditClassDialogComponent> implements OnInit {

    public class = {
        id: 0,
        key: null,
        name: '',
        class_type: 'other',
        subscription_type: 'open',
        invitations: '',
        is_researchable: 0,
        tag_id: null // new
    };
    public title = 'Edit Class';
    public file: any;

    // teacher available tags passed from parent
    public teacherTags: any[] = [];

    public selectedTagId: number = null;

    constructor(
        public dialogRef: MatDialogRef<EditClassDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.class) {
            this.class = this.data.class;
            if ((this.class as any).tag_id) { this.selectedTagId = (this.class as any).tag_id; }
        }
        if (this.data.title) {
            this.title = this.data.title;
        }
        if (this.data.tags) {
            this.teacherTags = this.data.tags;
            if (this.teacherTags.length === 1) {
                this.selectedTagId = this.teacherTags[0].id;
            }
        }
        this.resizeDialog();
    }

    fileChanged(e) {
        this.file = e.target.files[0];
        if (this.file) {
            const fileReader = new FileReader();
            fileReader.onload = () => {
                this.class.invitations = fileReader.result.toString();
            };
            fileReader.readAsText(this.file);
        }
    }

    onSave() {
        (this.class as any).tag_id = this.selectedTagId;
        this.dialogRef.close(this.class);
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '50vw';
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
