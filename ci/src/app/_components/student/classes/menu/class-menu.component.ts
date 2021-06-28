import {Component, Input, OnInit} from '@angular/core';
import {EmailTeacherDialogComponent} from '../email-teacher-dialog/email-teacher-dialog.component';
import {ClassesManagementService, UserService} from '../../../../_services';
import {MatSnackBar} from '@angular/material/snack-bar';
import {MatDialog} from '@angular/material/dialog';
import {DeviceDetectorService} from 'ngx-device-detector';

@Component({
    selector: 'app-my-class-menu',
    templateUrl: './class-menu.component.html',
    styleUrls: ['./class-menu.component.scss'],
    providers: [UserService, ClassesManagementService]
})
export class MyClassMenuComponent implements OnInit {

    @Input() classId: number;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    public myClasses = [];

    constructor(private classService: ClassesManagementService,
                private userService: UserService,
                public snackBar: MatSnackBar,
                public dialog: MatDialog,
                private deviceService: DeviceDetectorService) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
    }

    onEmail() {
        const classes = this.classService.classes;
        this.userService.getClasses()
            .subscribe(response => {
                this.myClasses = response['my_classes'];
                const item = this.myClasses.find(obj => {
                    return obj.id === this.classId;
                });
                const dialogRef = this.dialog.open(EmailTeacherDialogComponent, {
                    data: {'class': item},
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(mail => {
                    if (mail) {
                        this.classService.emailClass(this.classId, mail).subscribe(res => {
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
            });
    }

}
