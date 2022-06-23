import {Component, OnInit} from '@angular/core';
import {AuthenticationService, ShareService, ClassesManagementService, AssignmentService, TestService} from '../../../_services';
import {ActivatedRoute, Router} from '@angular/router';
import {MatDialog} from '@angular/material/dialog';
import {Sort} from '@angular/material/sort';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatSnackBar} from '@angular/material/snack-bar';
import {DeleteConfirmationDialogComponent, YesNoDialogComponent} from '../../dialogs';
import {User} from '../../../_models';
import {compare} from '../../../_helpers/compare.helper';

@Component({
    selector: 'app-share',
    templateUrl: './share.component.html',
    styleUrls: ['./share.component.scss'],
    providers: [ShareService, AssignmentService, TestService]
})
export class ShareComponent implements OnInit {

    public user: User;

    public type: string;
    public itemId: number;
    public item = {
        id: 0,
        teacher_id: 0,
        name: ''
    };
    public shares = [];
    public available_teachers = [];
    public selected_teachers = [];
    public email: string;
    public emailAvailable: string;
    public showAvailable = false;

    public dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    public backLink = 'dashboard';
    public backLinkText = 'Back';

    private sub: any;

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        public snackBar: MatSnackBar,
        private shareService: ShareService,
        private classService: ClassesManagementService,
        private assignmentService: AssignmentService,
        private testService: TestService,
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
            this.type = params['type'];
            if (!['classroom', 'assignment', 'test'].includes(this.type)) {
                this.router.navigate([this.backLink]);
            }
            this.itemId = +params['item_id'];
            if (this.type === 'classroom') {
                this.backLink = '/teacher/class';
                this.classService.getClass(this.itemId).subscribe(res => {
                    if (!res) {
                        this.router.navigate([this.backLink]);
                    }
                    this.item = res;
                    this.backLinkText = 'Share > Class > ' + (this.item ? this.item.name : this.itemId);
                });
            } else if (this.type === 'assignment') {
                this.backLink = '/teacher/' + this.type;
                this.assignmentService.getAssignment(this.itemId).subscribe(res => {
                    if (!res) {
                        this.router.navigate([this.backLink]);
                    }
                    this.item = res;
                    this.backLinkText = 'Share > Assignment > ' + (this.item ? this.item.name : this.itemId);
                });
            } else if (this.type === 'test') {
                this.backLink = '/teacher/' + this.type;
                this.testService.getTest(this.itemId).subscribe(res => {
                    if (!res) {
                        this.router.navigate([this.backLink]);
                    }
                    this.item = res;
                    this.backLinkText = 'Share > Test > ' + (this.item ? this.item.name : this.itemId);
                });
            }
            this.shareService.getShared(this.type, this.itemId)
                .subscribe(res => {
                    this.available_teachers = res['available_teachers'];
                    this.shares = res['shares'];
                });
        });
    }


    isAllSelected() {
        return this.available_teachers.length === this.selected_teachers.length;
    }

    masterToggle() {
        this.isAllSelected() ? this.selected_teachers = []
            : this.selected_teachers = this.available_teachers.map(s => s.id);
    }

    toggleTeacherChecked(teacher) {
        if (this.isTeacherChecked(teacher)) {
            this.selected_teachers = this.selected_teachers.filter(s => s !== teacher.id);
        } else {
            this.selected_teachers.push(teacher.id);
        }
    }

    isTeacherChecked(teacher) {
        return this.selected_teachers.filter(s => s === teacher.id).length > 0;
    }

    hasSelectedTeachers() {
        return this.selected_teachers.length > 0;
    }

    addShared() {
        let teachers_list = ``;
        Object.values(this.available_teachers).forEach(teacher => {
            if (this.selected_teachers.indexOf(teacher.id) >= 0) {
                teachers_list += `<br>`;
                if (teacher.first_name) {
                    teachers_list += ` ${teacher.first_name}`;
                }
                if (teacher.last_name) {
                    teachers_list += ` ${teacher.last_name}`;
                }
            }
        });
        const dialogRef = this.dialog.open(YesNoDialogComponent, {
            data: {
                'message': `You will be sending this classroom to the following teachers. ${teachers_list}<br> This action cannot be reversed.`
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.shareService.addShared(this.type, this.itemId, this.selected_teachers)
                    .subscribe(response => {
                        this.shares = response.shares;
                        this.available_teachers = response.available_teachers;
                        this.selected_teachers = [];
                        this.showAvailable = false;
                        this.snackBar.open('Teacher has been successfully added!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while adding teacher!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    deleteShared(teacherId) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                'message': 'Are you sure that you want to delete this?'
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.shareService.deleteShared(this.type, this.itemId, teacherId)
                    .subscribe(res => {
                        this.available_teachers.unshift(this.shares.filter(x => {
                            return +x.id === +teacherId;
                        })[0]);
                        this.shares = this.shares.filter(x => x.receiver.id !== teacherId);
                        this.snackBar.open('Successfully deleted!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                    }, error => {
                        this.snackBar.open('Unable to delete!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    sortData(sort: Sort, sortAvailableItems = false) {
        const data = sortAvailableItems ? this.available_teachers.slice() : this.shares.slice();
        if (!sort.active || sort.direction === '') {
            if (sortAvailableItems) {
                this.available_teachers = data;
            } else {
                this.shares = data;
            }
            return;
        }
        const sorted = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            if (sortAvailableItems) {
                switch (sort.active) {
                    case 'name':
                        return compare(a.first_name + ' ' + a.last_name, b.first_name + ' ' + b.last_name, isAsc);
                    case 'email':
                        return compare(a.email, b.email, isAsc);
                    default:
                        return 0;
                }
            } else {
                switch (sort.active) {
                    case 'name':
                        // tslint:disable-next-line:max-line-length
                        return compare(a.receiver.first_name + ' ' + a.receiver.last_name, b.receiver.first_name + ' ' + b.receiver.last_name, isAsc);
                    case 'email':
                        return compare(a.receiver.email, b.receiver.email, isAsc);
                    case 'receive_emails_from_students':
                        return compare(a.accepted, b.accepted, isAsc);
                    default:
                        return 0;
                }
            }
        });
        if (sortAvailableItems) {
            this.available_teachers = sorted;
        } else {
            this.shares = sorted;
        }
    }
}
