import {Component, Input, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {environment} from '../../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {TestReportDialogComponent} from '../../tests/test-report-dialog/test-report-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import {compare} from '../../../../../_helpers/compare.helper';

@Component({
    selector: 'app-class-tests-report',
    templateUrl: './class-tests-report.component.html',
    styleUrls: ['./class-tests-report.component.scss'],
    providers: []
})
export class ClassTestsReportComponent implements OnInit {

    @Input() tests: any[];
    @Input() students: any[];
    @Input() classId: number;
    @Input() forStudent = false;

    private readonly adminUrl = environment.adminUrl;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private sanitizer: DomSanitizer,
                private deviceService: DeviceDetectorService,
                public dialog: MatDialog
    ) {}

    ngOnInit() {
    }

    public getAttempts(tests) {
        const attempts = [];
        tests.forEach(test => {
            for (let i = 0; i < +test.attempts; i++) {
                attempts.push({...test, attempt: i});
            }
        });
        return attempts;
    }

    public getData(test, email, index = 0, data = null) {
        const students = Object.values(test.students);
        // @ts-ignore
        const studData = students.find(stud => stud.email === email);
        // @ts-ignore
        const attempt = (studData && studData.attempts) ? studData.attempts[index] : null;
        switch (data) {
            case 'is_started':
                return attempt && attempt.id;
            case 'is_finished':
                return attempt && (attempt.end_at || attempt.mark);
            case 'mark':
                return attempt && attempt.questions_count
                    ? (attempt.mark * 100).toFixed(0) + '%'
                        + ' (' + (attempt.mark * attempt.questions_count).toFixed(0)
                        + '/' + attempt.questions_count + ')'
                    : null;
            case 'progress':
                return attempt && attempt.questions_count ? attempt.mark * 100 : 0;
            default:
                break;
        }
    }

    onShowTestReport(item) {
        const dialogRef = this.dialog.open(TestReportDialogComponent, {
            data: {
                title: item.name + ': report',
                test: item
            },
            position: this.dialogPosition
        });
    }

    sortData(sort: Sort) {
        const data = this.tests.slice();
        if (!sort.active || sort.direction === '') {
            this.tests = data;
            return;
        }
        this.tests = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'start_at': return compare(a.start_at, b.start_at, isAsc, true);
                case 'due_at': return compare(a.due_at, b.due_at, isAsc, true);
                case 'attempts': return compare(a.attempts, b.attempts, isAsc);
                default: return 0;
            }
        });
    }

    setIcon(image) {
        if (!image) {
            image = 'images/default-icon.svg';
        }
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }
}
