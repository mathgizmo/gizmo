import {Component, Input, OnInit} from '@angular/core';
import {EmailClassDialogComponent} from '../email-class-dialog/email-class-dialog.component';
import {MatSnackBar} from '@angular/material/snack-bar';
import {MatDialog} from '@angular/material/dialog';
import {DeviceDetectorService} from 'ngx-device-detector';
import {ClassesManagementService} from "../../../../_services";

@Component({
    selector: 'app-class-menu',
    templateUrl: './class-menu.component.html',
    styleUrls: ['./class-menu.component.scss'],
})
export class ClassMenuComponent implements OnInit {

    @Input() classId: number;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    public classes = [];

    constructor(private classService: ClassesManagementService,
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
        this.classService.getClasses()
            .subscribe(response => {
                this.classes = response;
                const item = this.classes.find(obj => {
                    return obj.id === this.classId;
                });
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
            });
    }

}
