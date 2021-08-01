import {Component, OnInit, ViewChild} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {MatDialog} from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';

import {ClassesManagementService} from '../../../../_services';
import {DeviceDetectorService} from 'ngx-device-detector';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {ActivatedRoute} from '@angular/router';
import {TestReportDialogComponent} from '../../class/tests/test-report-dialog/test-report-dialog.component';
import {ClassAssignmentsCalendarComponent} from '../../class/assignments/calendar/class-assignments-calendar.component';
import {compare} from '../../../../_helpers/compare.helper';
import {SelectStudentsDialogComponent} from '../../class/assignments/select-students-dialog/select-students-dialog.component';

@Component({
    selector: 'app-research-tests',
    templateUrl: './research-tests.component.html',
    styleUrls: ['./research-tests.component.scss']
})
export class ResearchTestsComponent implements OnInit {

    classId: number;

    tests = [];
    class = {
        id: 0,
        name: ''
    };
    addTest = false;
    nameFilter;
    students = [];

    private readonly adminUrl = environment.adminUrl;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    public calendarView = false;

    public backLinkText = 'Back';

    private sub: any;

    @ViewChild('calendar') calendarComponent: ClassAssignmentsCalendarComponent;

    constructor(
        private route: ActivatedRoute,
        public snackBar: MatSnackBar,
        public dialog: MatDialog,
        private deviceService: DeviceDetectorService,
        private classService: ClassesManagementService,
        private sanitizer: DomSanitizer) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            this.classService.getClass(this.classId).subscribe(res => {
                this.class = res;
                this.backLinkText = 'Research > ' + (this.class ? this.class.name : this.classId) + ' > Tests';
            });
            this.classService.getTests(this.classId, { for_research: 1 })
                .subscribe(res => {
                    this.tests = res['tests'];
                });
            this.classService.getStudents(this.classId, false, { for_research: 1 }).subscribe(students => {
                this.students = students;
            });
        });
    }

    onShowTestReport(item) {
        this.dialog.open(TestReportDialogComponent, {
            data: {
                title: item.name + ': report',
                test: item,
                for_research: true
            },
            position: this.dialogPosition
        });
    }

    onShowTestStudents(item) {
        this.dialog.open(SelectStudentsDialogComponent, {
            data: {
                'title': item.name + ': assigned students',
                'students': this.students,
                'selected_students': item.students
            },
            position: this.dialogPosition
        });
    }

    onDownload(format = 'csv') {
        this.classService.downloadTestsReport(this.class.id, format, { for_research: 1 })
            .subscribe(file => {
                let type = 'text/csv;charset=utf-8;';
                switch (format) {
                    case 'xls':
                    case 'xlsx':
                        type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;';
                        break;
                    default:
                        break;
                }
                const newBlob = new Blob([file], { type: type });
                if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                    window.navigator.msSaveOrOpenBlob(newBlob);
                    return;
                }
                const data = window.URL.createObjectURL(newBlob);
                const link = document.createElement('a');
                link.href = data;
                link.download = this.class.name + ' - Tests Report.' + format;
                link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
                setTimeout(function () {
                    window.URL.revokeObjectURL(data);
                    link.remove();
                }, 100);
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
                case 'start_date': return compare(a.start_date, b.start_date, isAsc);
                case 'start_time': return compare(a.start_time, b.start_time, isAsc);
                case 'due_date': return compare(a.due_date, b.due_date, isAsc);
                case 'due_time': return compare(a.due_time, b.due_time, isAsc);
                case 'duration': return compare(a.duration, b.duration, isAsc);
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
