import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {DomSanitizer} from '@angular/platform-browser';
import {Sort} from '@angular/material/sort';
import {ClassesManagementService} from '../../../../../_services';
import {MatSnackBar} from '@angular/material/snack-bar';
import {DeviceDetectorService} from 'ngx-device-detector';
import {TestReportResetAttemptDialogComponent} from './reset-attempt-dialog/test-report-reset-attempt-dialog.component';
import {compare} from '../../../../../_helpers/compare.helper';

@Component({
    selector: 'app-test-report-dialog',
    templateUrl: 'test-report-dialog.component.html',
    styleUrls: ['test-report-dialog.component.scss']
})
export class TestReportDialogComponent extends BaseDialogComponent<TestReportDialogComponent> implements OnInit {

    public title = 'Test Report';
    public test = {
        class_id: 0,
        app_id: 0,
        attempts: 1,
        name: ''
    };
    public students = [];
    public attempts = [0];
    public email: string;
    public isForResearch = false;

    public dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(
        private classService: ClassesManagementService,
        private sanitizer: DomSanitizer,
        public snackBar: MatSnackBar,
        public dialog: MatDialog,
        private deviceService: DeviceDetectorService,
        public dialogRef: MatDialogRef<TestReportDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    public ngOnInit() {
        if (this.data.title) {
            this.title = this.data.title;
        }
        if (this.data.test) {
            this.test = this.data.test;
            this.attempts = Array(+this.test.attempts).fill(0).map((x, i) => i);
        }
        if (this.data.for_research) {
            this.isForResearch = this.data.for_research;
        }
        const filters = this.isForResearch ? { for_research: 1 } : null;
        this.classService.getTestReport(this.test.class_id, this.test.app_id, filters).subscribe(response => {
            this.students = response.students;
            this.students.forEach(stud => {
                stud.showDetail = false;
            });
        });
        this.resizeDialog();
    }

    onResetProgress(item) {
        const dialogRef = this.dialog.open(TestReportResetAttemptDialogComponent, {
            data: {
                item: item
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(attempt_id => {
            if (attempt_id) {
                this.classService.resetTestProgress(this.test.class_id, this.test.app_id, item.id, attempt_id)
                    .subscribe(response => {
                        item.attempts = item.attempts.filter(o => +o.id !== +attempt_id);
                        item.resets_count++;
                        this.snackBar.open('The attempt has been successfully reset!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                    });
            }
        });
    }

    onDownloadPDF(item) {
        this.classService.downloadTestReportPDF(this.test.class_id, this.test.app_id, item.id)
            .subscribe(file => {
                const newBlob = new Blob([file], { type: 'application/pdf' });
                if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                    window.navigator.msSaveOrOpenBlob(newBlob);
                    return;
                }
                const data = window.URL.createObjectURL(newBlob);
                const link = document.createElement('a');
                link.href = data;
                link.download = item.email + ' - ' + this.test.name + ' Test Report.pdf';
                link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
                setTimeout(function () {
                    window.URL.revokeObjectURL(data);
                    link.remove();
                }, 100);
            });
    }

    /* onDownloadPoorQuestionsPDF() {
        this.classService.downloadPoorQuestionsPDF(this.test.class_id, this.test.app_id)
            .subscribe(file => {
                const newBlob = new Blob([file], { type: 'application/pdf' });
                if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                    window.navigator.msSaveOrOpenBlob(newBlob);
                    return;
                }
                const data = window.URL.createObjectURL(newBlob);
                const link = document.createElement('a');
                link.href = data;
                link.download = this.test.name + ' - Poorly answered questions.pdf';
                link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
                setTimeout(function () {
                    window.URL.revokeObjectURL(data);
                    link.remove();
                }, 100);
            });
    } */

    onShowDetails(item, attempt) {
        item.showDetail = !item.showDetail;
        if (!item.showDetail && item.selectedAttempt !== attempt) {
            item.showDetail = true;
        }
        if (item.showDetail) {
            item.selectedAttempt = attempt;
            this.classService.getTestDetails(this.test.class_id, this.test.app_id, item.id, item.attempts[attempt].id)
                .subscribe(response => {
                    item.details = response.data;
                });
        }
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '80vw';
        this.dialogRef.updateSize(width);
    }

    sortData(sort: Sort) {
        const data = this.students.slice();
        if (!sort.active || sort.direction === '') {
            this.students = data;
            return;
        }
        this.students = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'email': return compare(a.email, b.email, isAsc);
                case 'mark': return compare(a.mark, b.mark, isAsc);
                case 'attempts_count': return compare(a.attempts_count, b.attempts_count, isAsc);
                case 'resets_count': return compare(a.resets_count, b.resets_count, isAsc);
                case 'completed_at': return compare(a.completed_at, b.completed_at, isAsc, true);
                default: return 0;
            }
        });
    }

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        /* if (event.key === 'Enter') {
            this.dialogRef.close();
        } */
    }
}
