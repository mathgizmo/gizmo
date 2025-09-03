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
    tags: [] as Array<{id:number,name:string}>
    };
    public title = 'Edit Class';
    public file: any;

    // teacher available tags passed from parent
    public teacherTags: any[] = [];
    public selectedTagIds: number[] = [];
    // existing student emails for 'assigned' subscription type (read-only display)
    public existingStudentEmails: string[] = [];
    public pendingInvitations: string[] = []; // emails just added but not yet present as students
    public showAssignedStudents = false; // collapsed by default; restored from storage if available

    constructor(
        public dialogRef: MatDialogRef<EditClassDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.class) {
            this.class = this.data.class;
            const current = (this.class as any)?.tags ? ((this.class as any).tags as any[]).map(t => +t.id) : [];
            this.selectedTagIds = [...current];
        }
        if (this.data.title) {
            this.title = this.data.title;
        }
    if (this.data.tags) { this.teacherTags = this.data.tags; }
        if (this.data.studentsEmails && this.class.subscription_type === 'assigned') {
            this.existingStudentEmails = this.data.studentsEmails;
            // Merge any pending invitations stored locally for this class (not yet materialized as students)
            const pendingKey = this.getPendingStorageKey();
            const stored = localStorage.getItem(pendingKey);
            if (stored) {
                const arr = JSON.parse(stored) as string[];
                // keep only those not already existing
                this.pendingInvitations = arr.filter(e => this.existingStudentEmails.indexOf(e) === -1);
            }
            // restore collapse state if saved
            const collapseKey = this.getCollapseStorageKey();
            const collapsedStored = localStorage.getItem(collapseKey);
            if (collapsedStored !== null) {
                this.showAssignedStudents = collapsedStored === '1';
            }
            // Ensure we don't pre-populate textarea with existing emails (only for new additions)
            this.class.invitations = '';
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
        const payload = { ...this.class } as any;
        payload.tag_ids = this.selectedTagIds;
        // Track new invitations locally for pending marker next time (assigned type only)
        if (this.class.subscription_type === 'assigned' && this.class.invitations) {
            const newEmails = this.class.invitations.split(/[,\n;]/).map(e => e.trim()).filter(e => !!e);
            if (newEmails.length) {
                const set = new Set<string>([...this.pendingInvitations, ...newEmails]);
                localStorage.setItem(this.getPendingStorageKey(), JSON.stringify(Array.from(set.values())));
            }
        }
        this.dialogRef.close(payload);
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

    private getPendingStorageKey(): string {
        return 'class_pending_invites_' + (this.class?.id || 'new');
    }

    private getCollapseStorageKey(): string {
        return 'class_assigned_show_' + (this.class?.id || 'new');
    }

    toggleAssignedList() {
        this.showAssignedStudents = !this.showAssignedStudents;
        // store as 1 for show, 0 for hidden
        localStorage.setItem(this.getCollapseStorageKey(), this.showAssignedStudents ? '1' : '0');
    }
}
