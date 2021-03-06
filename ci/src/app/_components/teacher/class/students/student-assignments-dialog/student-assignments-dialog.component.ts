import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA, MatDialog} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {Sort} from '@angular/material/sort';
import {DeviceDetectorService} from 'ngx-device-detector';
import {environment} from '../../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {compare} from '../../../../../_helpers/compare.helper';
import {ClassesManagementService} from '../../../../../_services';

@Component({
    selector: 'app-student-assignments-dialog',
    templateUrl: 'student-assignments-dialog.component.html',
    styleUrls: ['student-assignments-dialog.component.scss'],
    providers: [ClassesManagementService]
})
export class StudentAssignmentsDialogComponent extends BaseDialogComponent<StudentAssignmentsDialogComponent> implements OnInit {

    public assignments = [];
    public student: any;
    public classId: number;

    public dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    private readonly adminUrl = environment.adminUrl;

    constructor(private classService: ClassesManagementService,
        public dialog: MatDialog, private deviceService: DeviceDetectorService,
        private sanitizer: DomSanitizer,
        public dialogRef: MatDialogRef<StudentAssignmentsDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.student) {
            // tslint:disable-next-line:indent
        	this.student = data.student;
        }
        if (data.class_id) {
            // tslint:disable-next-line:indent
        	this.classId = data.class_id;
        }
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    public ngOnInit() {
        this.resizeDialog();
        this.classService.getStudentAssignmentsReport(this.classId, this.student.id)
            .subscribe(items => {
                this.assignments = items;
            });
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '86vw' : '70vw';
        this.dialogRef.updateSize(width);
    }

    sortData(sort: Sort) {
        const data = this.assignments.slice();
        if (!sort.active || sort.direction === '') {
            this.assignments = data;
            return;
        }
        this.assignments = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'due_at': return compare(a.due_at, b.due_at, isAsc);
                case 'completed_at': return compare(a.completed_at, b.completed_at, isAsc);
                case 'lessons_complete': return compare(a.lessons_complete, b.lessons_complete, isAsc);
                case 'correct_rate': return compare(a.correct_rate, b.correct_rate, isAsc);
                default: return 0;
            }
        });
    }

    setIcon(image) {
        if (!image) { image = 'images/default-icon.svg'; }
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) { }
}
