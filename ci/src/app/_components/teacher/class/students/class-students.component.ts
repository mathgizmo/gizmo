import {Component, OnInit} from '@angular/core';
import {ClassesManagementService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import {MatDialog} from '@angular/material/dialog';
import {Sort} from '@angular/material/sort';
import {StudentAssignmentsDialogComponent} from '../../class/students/student-assignments-dialog/student-assignments-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {AddStudentDialogComponent} from './add-student-dialog/add-student-dialog.component';
import {MatSnackBar} from '@angular/material/snack-bar';
import {DeleteConfirmationDialogComponent} from '../../../dialogs/index';

@Component({
    selector: 'app-class-students',
    templateUrl: './class-students.component.html',
    styleUrls: ['./class-students.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassStudentsComponent implements OnInit {

    classId: number;
    class = {
        name: ''
    };

    students = [];

    public name: string;
    public first_name: string;
    public last_name: string;
    public email: string;

    dialogPosition: any;
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
            this.classService.getStudents(this.classId)
                .subscribe(students => {
                    this.students = students;
                });
        });
    }

    deleteStudent(studentId) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                // 'message': 'Are you sure that you want to delete this student from the class?'
            },
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
            }
        });
    }

    addStudent() {
        const dialogRef = this.dialog.open(AddStudentDialogComponent, {
            data: { },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.addStudent(this.classId, result)
                    .subscribe(item => {
                        if (item && item.id) {
                            this.students.unshift(item);
                            this.snackBar.open('Student was successfully added to the classroom!', '', {
                                duration: 3000,
                                panelClass: ['success-snackbar']
                            });
                        } else {
                            this.snackBar.open('Unable to add student with email you provided to the classroom!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        }
                    }, error => {
                        this.snackBar.open('Unable to add student with email you provided to the classroom!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
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
                case 'name': return compare(a.name, b.name, isAsc);
                case 'first_name': return compare(a.first_name, b.first_name, isAsc);
                case 'last_name': return compare(a.last_name, b.last_name, isAsc);
                case 'email': return compare(a.email, b.email, isAsc);
                case  'assignments_finished_count': return compare(a.assignments_finished_count, b.assignments_finished_count, isAsc);
                case  'assignments_past_due_count': return compare(a.assignments_past_due_count, b.assignments_past_due_count, isAsc);
                default: return 0;
            }
        });
    }

    showAssignments(student) {
        const dialogRef = this.dialog.open(StudentAssignmentsDialogComponent, {
            data: { 'assignments': student.assignments, 'student': student},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {});
    }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    if (typeof a === 'string' || typeof b === 'string') {
        a = ('' + a).toLowerCase();
        b = ('' + b).toLowerCase();
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}