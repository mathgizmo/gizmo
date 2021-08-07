import {Component, OnInit} from '@angular/core';
import {ClassesManagementService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import {MatDialog} from '@angular/material/dialog';
import {Sort} from '@angular/material/sort';
import {StudentAssignmentsDialogComponent} from '../../class/students/student-assignments-dialog/student-assignments-dialog.component';
import {StudentTestsDialogComponent} from '../../class/students/student-tests-dialog/student-tests-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatSnackBar} from '@angular/material/snack-bar';
import {compare} from '../../../../_helpers/compare.helper';
// tslint:disable-next-line:max-line-length
import {EditStudentResearchStatusDialogComponent} from './edit-student-research-status-dialog/edit-student-research-status-dialog.component';

@Component({
    selector: 'app-research-students',
    templateUrl: './research-students.component.html',
    styleUrls: ['./research-students.component.scss']
})
export class ResearchStudentsComponent implements OnInit {

    public classId: number;
    public class = {
        id: 0,
        name: ''
    };
    public students = [];

    public email: string;

    public dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    public backLinkText = 'Back';

    private sub: any;

    constructor(
        private route: ActivatedRoute,
        public snackBar: MatSnackBar,
        private classService: ClassesManagementService,
        public dialog: MatDialog,
        private deviceService: DeviceDetectorService) {
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
                this.backLinkText = 'Research > ' + (this.class ? this.class.name : this.classId) + ' > Students';
            });
            this.classService.getStudents(this.classId, true, null)
                .subscribe(students => {
                    this.students = students;
                });
        });
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
                case 'name': return compare(a.first_name + ' ' + a.last_name, b.first_name + ' ' + b.last_name, isAsc);
                case 'first_name': return compare(a.first_name, b.first_name, isAsc);
                case 'last_name': return compare(a.last_name, b.last_name, isAsc);
                case 'email': return compare(a.email, b.email, isAsc);
                case 'assignments_finished_count': return compare(a.assignments_finished_count, b.assignments_finished_count, isAsc);
                case 'tests_finished_count': return compare(a.tests_finished_count, b.tests_finished_count, isAsc);
                case 'is_element1_accepted': return compare(a.pivot.is_element1_accepted, b.pivot.is_element1_accepted, isAsc);
                case 'is_element2_accepted': return compare(a.pivot.is_element2_accepted, b.pivot.is_element2_accepted, isAsc);
                case 'is_element3_accepted': return compare(a.pivot.is_element3_accepted, b.pivot.is_element3_accepted, isAsc);
                case 'is_element4_accepted': return compare(a.pivot.is_element4_accepted, b.pivot.is_element4_accepted, isAsc);
                default: return 0;
            }
        });
    }

    showAssignments(student) {
        const dialogRef = this.dialog.open(StudentAssignmentsDialogComponent, {
            data: { 'class_id': this.classId, 'student': student},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {});
    }

    showTests(student) {
        const dialogRef = this.dialog.open(StudentTestsDialogComponent, {
            data: { 'class_id': this.classId, 'student': student},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {});
    }

    onDownload(format = 'csv') {
        this.classService.downloadStudents(this.class.id, format)
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
                link.download = this.class.name + ' - Students.' + format;
                link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
                setTimeout(function () {
                    window.URL.revokeObjectURL(data);
                    link.remove();
                }, 100);
            });
    }

    editStatus(student) {
        const dialogRef = this.dialog.open(EditStudentResearchStatusDialogComponent, {
            data: { student: student }, position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                const item = {
                    id: student.id,
                    ...result
                };
                this.classService.changeStudent(this.classId, item)
                    .subscribe(res => {
                        student.pivot = {
                            is_consent_read: +result.is_consent_read,
                            is_element1_accepted: result.is_element1_accepted,
                            is_element2_accepted: +result.is_element2_accepted,
                            is_element3_accepted: +result.is_element3_accepted,
                            is_element4_accepted: +result.is_element4_accepted,
                        };
                        this.snackBar.open('Research status was successfully updated!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                    }, error => {
                        this.snackBar.open('Unable to update research status!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }
}
