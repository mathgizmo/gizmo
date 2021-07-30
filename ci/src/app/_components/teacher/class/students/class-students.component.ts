import {Component, OnInit} from '@angular/core';
import {ClassesManagementService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import {MatDialog} from '@angular/material/dialog';
import {Sort} from '@angular/material/sort';
import {StudentAssignmentsDialogComponent} from '../../class/students/student-assignments-dialog/student-assignments-dialog.component';
import {StudentTestsDialogComponent} from '../../class/students/student-tests-dialog/student-tests-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {AddStudentDialogComponent} from './add-student-dialog/add-student-dialog.component';
import {MatSnackBar} from '@angular/material/snack-bar';
import {DeleteConfirmationDialogComponent, YesNoDialogComponent} from '../../../dialogs/index';
import {compare} from '../../../../_helpers/compare.helper';

@Component({
    selector: 'app-class-students',
    templateUrl: './class-students.component.html',
    styleUrls: ['./class-students.component.scss']
})
export class ClassStudentsComponent implements OnInit {

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
            const classes = this.classService.classes;
            this.class = classes.filter(x => x.id === this.classId)[0];
            this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Students';
            this.classService.getStudents(this.classId, true)
                .subscribe(students => {
                    this.students = students;
                });
        });
    }

    deleteStudent(student) {
        const studentId = student.id;
        const dialogRef = student.is_unsubscribed
            ? this.dialog.open(YesNoDialogComponent, {
                data: {
                    message: `Student (${student.email}) has indicated that they wish to unsubscribe from this class. If you press 'yes' they will be removed from your class list and will lose information about their progress. If you press 'no' they will be resubscribed to the class.`
                }, position: this.dialogPosition
            })
            : this.dialog.open(DeleteConfirmationDialogComponent, {
                data: {
                    message: `Are you sure that you want to delete the student (${student.email}) from the class? If you press 'yes' they will be removed from your class list and will lose information about their progress.` },
                position: this.dialogPosition
            });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.deleteStudent(this.classId, studentId)
                    .subscribe(res => {
                        this.students = this.students.filter(x => x.id !== studentId);
                        this.snackBar.open('Student was successfully deleted from the classroom!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                    }, error => {
                        this.snackBar.open('Unable to delete student from the classroom!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            } else if (student.is_unsubscribed) {
                this.classService.addStudent(this.classId, studentId)
                    .subscribe(res => {
                        student.is_unsubscribed = false;
                        this.snackBar.open('Student was successfully resubscribed to the classroom!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                    }, error => {
                        this.snackBar.open('Unable to resubscribed student to the classroom!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    addStudents() {
        const dialogRef = this.dialog.open(AddStudentDialogComponent, {
            data: { },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(students => {
            if (students) {
                this.classService.addStudents(this.classId, students)
                    .subscribe(items => {
                        if (items && items.length) {
                            items.forEach(item => {
                                this.students.unshift(item);
                            });
                            this.snackBar.open('Students were successfully added to the classroom!', '', {
                                duration: 3000,
                                panelClass: ['success-snackbar']
                            });
                        }
                    }, error => {
                        this.snackBar.open('Unable to add students to the classroom!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onTestDurationChanged(item, newDuration) {
        item.test_duration_multiply_by = newDuration;
        this.classService.changeStudent(this.classId, item)
            .subscribe(assignments => {
                this.snackBar.open('Test Duration Multiplier Saved!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                this.snackBar.open('Error occurred while saving Test Duration Multiplier!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
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
                case 'created_at': return compare(a.created_at, b.created_at, isAsc);
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
}
