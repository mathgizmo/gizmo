import {Component, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {ClassesManagementService} from '../../../_services/classes-management.service';
import {EditClassDialogComponent} from './edit-class-dialog/edit-class-dialog.component';
import {EmailClassDialogComponent} from './email-class-dialog/email-class-dialog.component';
import {DeleteConfirmationDialogComponent} from '../../dialogs/index';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import {MatSnackBar} from '@angular/material/snack-bar';

@Component({
    selector: 'app-manage-classes',
    templateUrl: './manage-classes.component.html',
    styleUrls: ['./manage-classes.component.scss'],
    providers: [ClassesManagementService]
})
export class ManageClassesComponent implements OnInit {

    public classes = [];

    public id: number;
    public name: string;
    public subscription_type: string;
    public class_type: string;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private classService: ClassesManagementService, public dialog: MatDialog,
                private deviceService: DeviceDetectorService, public snackBar: MatSnackBar) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.classService.getClasses()
            .subscribe(response => {
                this.classes = response;
            });
    }

    onAddClass() {
        const dialogRef = this.dialog.open(EditClassDialogComponent, {
            data: { 'title': 'Create Class' },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.addClass(result)
                    .subscribe(item => {
                        this.classes.unshift(item);
                        this.snackBar.open('Class have been successfully created!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while creating class!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onEditClass(item) {
        const dialogRef = this.dialog.open(EditClassDialogComponent, {
            data: { 'title': 'Edit Class', 'class': item},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.updateClass(item.id, result).subscribe(res => {
                    this.snackBar.open('Class have been successfully updated!', '', {
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
                    this.snackBar.open(message ? message : 'Error occurred while updating class!', '', {
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
                        this.snackBar.open('Class have been successfully deleted!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while deleting class!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    onEmail(item) {
        const dialogRef = this.dialog.open(EmailClassDialogComponent, {
            data: { 'class': item },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(mail => {
            if (mail) {
                this.classService.emailClass(item.id, mail).subscribe(res => {
                    this.snackBar.open('Email have been successfully sent!', '', {
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
                    this.snackBar.open(message ? message : 'Error occurred while sending email!', '', {
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

function compare(a: number | string, b: number | string, isAsc: boolean) {
    if (typeof a === 'string' || typeof b === 'string') {
        a = ('' + a).toLowerCase();
        b = ('' + b).toLowerCase();
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
