import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../../dialogs/base-dialog.component';
import {DomSanitizer} from '@angular/platform-browser';
import {Sort} from '@angular/material/sort';
import {ClassesManagementService} from '../../../../../_services';

@Component({
    selector: 'app-test-report-dialog',
    templateUrl: 'test-report-dialog.component.html',
    styleUrls: ['test-report-dialog.component.scss'],
    providers: [ClassesManagementService]
})
export class TestReportDialogComponent extends BaseDialogComponent<TestReportDialogComponent> {

    title = 'Test Report';
    test = {
        class_id: 0,
        app_id: 0
    };
    students = [];
    public email: string;

    constructor(
        private classService: ClassesManagementService,
        private sanitizer: DomSanitizer,
        public dialogRef: MatDialogRef<TestReportDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.title) {
            // tslint:disable-next-line:indent
            this.title = data.title;
        }
        if (data.test) {
            // tslint:disable-next-line:indent
            this.test = data.test;
        }
        this.classService.getTestReport(this.test.class_id, this.test.app_id).subscribe(response => {
            this.students = response.students;
            this.students.forEach(stud => {
                stud.showDetail = false;
            });
        });
    }

    onResetProgress(item) {
        this.classService.resetTestProgress(this.test.class_id, this.test.app_id, [item.id])
            .subscribe(response => {
                item.mark = null;
                item.start_at = null;
                item.end_at = null;
                item.details = [];
            });
    }

    onShowDetails(item) {
        item.showDetail = !item.showDetail;
        if (item.showDetail) {
            this.classService.getTestDetails(this.test.class_id, this.test.app_id, item.id)
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
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'first_name': return compare(a.first_name, b.first_name, isAsc);
                case 'last_name': return compare(a.last_name, b.last_name, isAsc);
                case 'email': return compare(a.email, b.email, isAsc);
                case 'mark': return compare(a.mark, b.mark, isAsc);
                case 'start_at': return compare(a.start_at, b.start_at, isAsc);
                case 'end_at': return compare(a.end_at, b.end_at, isAsc);
                default: return 0;
            }
        });
    }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    if (typeof a === 'string' || typeof b === 'string') {
        a = ('' + a).toLowerCase();
        b = ('' + b).toLowerCase();
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
