import {Component, OnInit} from '@angular/core';
import {AuthenticationService, ClassesManagementService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import {MatDialog} from '@angular/material/dialog';
import {Sort} from '@angular/material/sort';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatSnackBar} from '@angular/material/snack-bar';
import {DeleteConfirmationDialogComponent} from '../../../dialogs/index';
import {User} from '../../../../_models';
import {compare} from '../../../../_helpers/compare.helper';

@Component({
    selector: 'app-class-researchers',
    templateUrl: './class-researchers.component.html',
    styleUrls: ['./class-researchers.component.scss']
})
export class ClassResearchersComponent implements OnInit {

    public user: User;

    public classId: number;
    public class = {
        id: 0,
        teacher_id: 0,
        name: ''
    };
    public researchers = [];
    public available_researchers = [];
    public email: string;
    public emailAvailable: string;
    public showAvailable = false;

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
        private authenticationService: AuthenticationService,
        public dialog: MatDialog,
        private deviceService: DeviceDetectorService) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.user = this.authenticationService.userValue;
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            const classes = this.classService.classes;
            this.class = classes.filter(x => x.id === this.classId)[0];
            this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Researchers';
            this.classService.getTeachers(this.classId, { is_researcher: true })
                .subscribe(res => {
                    this.available_researchers = res['available_teachers'];
                    this.researchers = res['teachers'];
                });
        });
    }

    addResearcher(teacher) {
        this.classService.addTeacher(this.classId, teacher.id, { is_researcher: true })
            .subscribe(newTeacher => {
                this.researchers.unshift(teacher);
                this.available_researchers = this.available_researchers.filter(x => {
                    return +x.id !== +teacher.id;
                });
                this.showAvailable = false;
                this.snackBar.open('Researcher has been successfully added!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }, error => {
                let message = '';
                if (typeof error === 'object') {
                    Object.values(error).forEach(x => {
                        message += x + ' ';
                    });
                } else {
                    message = error;
                }
                this.snackBar.open(message ? message : 'Error occurred while adding researcher!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    deleteResearcher(teacherId) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                'message': 'Are you sure that you want to delete this teacher from the class?'
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.deleteTeacher(this.classId, teacherId)
                    .subscribe(res => {
                        this.available_researchers.unshift(this.researchers.filter(x => {
                            return +x.id === +teacherId;
                        })[0]);
                        this.researchers = this.researchers.filter(x => x.id !== teacherId);
                        this.snackBar.open('Researcher was successfully deleted from the classroom!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                    }, error => {
                        this.snackBar.open('Unable to delete researcher from the classroom!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    sortData(sort: Sort, sortAvailableItems = false) {
        const data = sortAvailableItems ? this.available_researchers.slice() : this.researchers.slice();
        if (!sort.active || sort.direction === '') {
            if (sortAvailableItems) {
                this.available_researchers = data;
            } else {
                this.researchers = data;
            }
            return;
        }
        const sorted = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.first_name + ' ' + a.last_name, b.first_name + ' ' + b.last_name, isAsc);
                case 'first_name': return compare(a.first_name, b.first_name, isAsc);
                case 'last_name': return compare(a.last_name, b.last_name, isAsc);
                case 'email': return compare(a.email, b.email, isAsc);
                default: return 0;
            }
        });
        if (sortAvailableItems) {
            this.available_researchers = sorted;
        } else {
            this.researchers = sorted;
        }
    }
}
