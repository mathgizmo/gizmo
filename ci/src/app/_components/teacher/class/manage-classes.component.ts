import {Component, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {EditClassDialogComponent} from './edit-class-dialog/edit-class-dialog.component';
import {DeleteConfirmationDialogComponent, YesNoDialogComponent} from '../../dialogs/index';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import {MatSnackBar} from '@angular/material/snack-bar';
import {Router} from '@angular/router';
import {
    ClassesManagementService,
    AuthenticationService,
    ShareService,
    AssignmentService,
    TestService
} from '../../../_services';
import {User} from '../../../_models';
import {compare} from '../../../_helpers/compare.helper';

@Component({
    selector: 'app-manage-classes',
    templateUrl: './manage-classes.component.html',
    styleUrls: ['./manage-classes.component.scss'],
    providers: [ShareService]
})
export class ManageClassesComponent implements OnInit {

    public user: User;

    public classes = [];

    public id: number;
    public name: string;
    public subscription_type: string;
    public class_type: string;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private classService: ClassesManagementService,
                private shareService: ShareService,
                private authenticationService: AuthenticationService,
                public dialog: MatDialog,
                private router: Router,
                private deviceService: DeviceDetectorService,
                public snackBar: MatSnackBar) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.user = this.authenticationService.userValue;

        this.checkNewShares();
    }

    checkNewShares() {
        this.shareService.getNewShare('classroom').subscribe(res => {
            if (res.item) {
                const dialogRef = this.dialog.open(YesNoDialogComponent, {
                    data: { 'message': `You have been sent<br> <b>${res.item.classroom.name}</b><br> by <b>${res.item.sender.first_name} ${res.item.sender.last_name}</b><br>are you willing to accept it into your classrooms list?<br><br><div><small style="font-size: 70%">If you do not accept this classroom it will be removed from your list.</small><br><small style="font-size: 70%">If you accept the classroom, you can use it, remove it or modify it as you wish.</small></div><br>`,
                        'text_yes': 'Accept',
                        'text_no': 'Decline'
                    },
                    position: this.dialogPosition,
                    disableClose: true
                });
                dialogRef.afterClosed().subscribe(result => {
                    this.shareService.newShareToggle('classroom', res.item.item_id, result).subscribe(() => {
                        return this.checkNewShares();
                    });
                });
            } else {
                this.classService.getClasses()
                    .subscribe(response => {
                        this.classes = response;
                    });
            }
        });
    }

    onAddClass() {
        const dialogRef = this.dialog.open(EditClassDialogComponent, {
            data: { 'message': 'Create Classroom' },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.addClass(result)
                    .subscribe(item => {
                        this.classes.unshift(item);
                        this.snackBar.open('Classroom has been successfully created!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
                        if (item.subscription_type === 'invitation') {
                            this.router.navigate(['teacher/class/' + item.id + '/invitation-settings']);
                        }
                    }, error => {
                        let message = '';
                        if (typeof error === 'object') {
                            Object.values(error).forEach(x => {
                                message += x + ' ';
                            });
                        } else {
                            message = error;
                        }
                        this.snackBar.open(message ? message : 'Error occurred while creating classroom!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onEditClass(item) {
        const dialogRef = this.dialog.open(EditClassDialogComponent, {
            data: { 'title': 'Edit Classroom', 'class': item},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.updateClass(item.id, result).subscribe(res => {
                    this.snackBar.open('Classroom has been successfully updated!', '', {
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
                    this.snackBar.open(message ? message : 'Error occurred while updating classroom!', '', {
                        duration: 3000,
                        panelClass: ['error-snackbar']
                    });
                });
            }
        });
    }

    onDeleteClass(class_id) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                // 'message': 'Are you sure that you want to delete the class?'
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.deleteClass(class_id)
                    .subscribe(response => {
                        this.classes = this.classes.filter( (item) => {
                            return item.id !== class_id;
                        });
                        this.snackBar.open('Classroom has been successfully deleted!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while deleting classroom!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    sortData(sort: Sort) {
        const data = this.classes.slice();
        if (!sort.active || sort.direction === '') {
            this.classes = data;
            return;
        }
        this.classes = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'class_type': return compare(a.class_type, b.class_type, isAsc);
                case 'subscription_type': return compare(a.subscription_type, b.subscription_type, isAsc);
                default: return 0;
            }
        });
    }

}


