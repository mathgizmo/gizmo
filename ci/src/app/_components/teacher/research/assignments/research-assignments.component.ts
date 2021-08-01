import {Component, OnInit, ViewChild} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {MatDialog} from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';

import {ClassesManagementService} from '../../../../_services';
import {DeviceDetectorService} from 'ngx-device-detector';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {ActivatedRoute} from '@angular/router';
import {ClassAssignmentsCalendarComponent} from '../../class/assignments/calendar/class-assignments-calendar.component';
import {AssignmentReportDialogComponent} from '../../class/assignments/assignment-report-dialog/assignment-report-dialog.component';
import {compare} from '../../../../_helpers/compare.helper';
import {SelectStudentsDialogComponent} from '../../class/assignments/select-students-dialog/select-students-dialog.component';

@Component({
    selector: 'app-research-assignments',
    templateUrl: './research-assignments.component.html',
    styleUrls: ['./research-assignments.component.scss']
})
export class ResearchAssignmentsComponent implements OnInit {

    classId: number;

    assignments = [];
    class = {
        id: 0,
        name: ''
    };
    addAssignment = false;
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
        public dialog: MatDialog, private deviceService: DeviceDetectorService,
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
                this.backLinkText = 'Research > ' + (this.class ? this.class.name : this.classId) + ' > Assignments';
            });
            this.classService.getAssignments(this.classId, { for_research: 1 })
                .subscribe(res => {
                    this.assignments = res['assignments'];
                });
            this.classService.getStudents(this.classId, false, { for_research: 1 }).subscribe(students => {
                this.students = students;
            });
        });
    }

    onShowAssignmentReport(item) {
        this.classService.getReport(this.classId, { for_research: 1 })
            .subscribe(response => {
                const students = response.students;
                this.dialog.open(AssignmentReportDialogComponent, {
                    data: {
                        title: item.name + ': report',
                        assignment: item,
                        students: students
                    },
                    position: this.dialogPosition
                });
            });
    }

    onShowAssignmentStudents(item) {
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
        this.classService.downloadAssignmentsReport(this.class.id, format, { for_research: 1 })
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
                link.download = this.class.name + ' - Assignments Report.' + format;
                link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
                setTimeout(function () {
                    window.URL.revokeObjectURL(data);
                    link.remove();
                }, 100);
            });
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
                case 'start_date': return compare(a.start_date, b.start_date, isAsc);
                case 'start_time': return compare(a.start_time, b.start_time, isAsc);
                case 'due_date': return compare(a.due_date, b.due_date, isAsc);
                case 'due_time': return compare(a.due_time, b.due_time, isAsc);
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
