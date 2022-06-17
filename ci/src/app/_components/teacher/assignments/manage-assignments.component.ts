import {Component, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {AssignmentService} from '../../../_services/assignment.service';
import {EditAssignmentDialogComponent} from './edit-assignment-dialog/edit-assignment-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import {DomSanitizer} from '@angular/platform-browser';
import {environment} from '../../../../environments/environment';
import {ActivatedRoute} from '@angular/router';
import {DeleteConfirmationDialogComponent, YesNoDialogComponent} from '../../dialogs/index';
import {MatSnackBar} from '@angular/material/snack-bar';
import {compare} from '../../../_helpers/compare.helper';
import {User} from '../../../_models';
import {AuthenticationService, ShareService} from '../../../_services';

@Component({
    selector: 'app-manage-assignments',
    templateUrl: './manage-assignments.component.html',
    styleUrls: ['./manage-assignments.component.scss'],
    providers: [AssignmentService, ShareService]
})
export class ManageAssignmentsComponent implements OnInit {
    public user: User;

    public assignments = [];
    public icons = [];
    public name: string;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    private readonly adminUrl = environment.adminUrl;

    constructor(private route: ActivatedRoute, private assignmentService: AssignmentService, private sanitizer: DomSanitizer,
                private authenticationService: AuthenticationService,
                private shareService: ShareService,
                public dialog: MatDialog, private deviceService: DeviceDetectorService, public snackBar: MatSnackBar) {
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
        this.shareService.getNewShare('assignment').subscribe(res => {
            if (res.item) {
                const dialogRef = this.dialog.open(YesNoDialogComponent, {
                    data: { 'message': `You have been sent<br> <b>${res.item.assignment.name}</b><br> by <b>${res.item.sender.email}</b><br>are you willing to accept it into your assignments list?<br><br><div><small style="font-size: 70%">If you do not accept this assignment it will be removed from your list.</small><br><small style="font-size: 70%">If you accept the assignment, you can use it, remove it or modify it as you wish.</small></div><br>`,
                        'text_yes': 'Accept',
                        'text_no': 'Decline'
                    },
                    position: this.dialogPosition,
                    disableClose: true
                });
                dialogRef.afterClosed().subscribe(result => {
                    this.shareService.newShareToggle('assignment', res.item.item_id, result).subscribe(() => {
                        return this.checkNewShares();
                    });
                });
            } else {
                this.assignmentService.getAssignments()
                    .subscribe(response => {
                        this.assignments = response;
                    });
                this.assignmentService.getAvailableIcons()
                    .subscribe(response => {
                        this.icons = response;
                    });
            }
        });
    }

    onAddAssignment() {
        this.assignmentService.getAppTree()
            .subscribe(tree => {
                const dialogRef = this.dialog.open(EditAssignmentDialogComponent, {
                    data: { 'title': 'Create Assignment', 'icons': this.icons, 'tree': tree },
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
                        this.assignmentService.addAssignment(result)
                            .subscribe(item => {
                                if (item) {
                                    this.assignments.unshift(item);
                                    this.snackBar.open('Assignment has been successfully created!', '', {
                                        duration: 3000,
                                        panelClass: ['success-snackbar']
                                    });
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
                                this.snackBar.open(message ? message : 'Error occurred while creating assignment!', '', {
                                    duration: 3000,
                                    panelClass: ['error-snackbar']
                                });
                            });
                    }
                });
            });
    }

    onEditAssignment(item) {
        this.assignmentService.getAppTree(item.id)
            .subscribe(tree => {
                const dialogRef = this.dialog.open(EditAssignmentDialogComponent, {
                    data: { 'title': 'Edit Assignment', 'assignment': item, 'icons': this.icons, 'tree': tree },
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
                        this.assignmentService.updateAssignment(item.id, result).subscribe(res => {
                            this.snackBar.open('Assignment has been successfully updated!', '', {
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
                            this.snackBar.open(message ? message : 'Error occurred while updating assignment!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                    }
                });
            });
    }

    onCopyAssignment(item) {
        this.assignmentService.copyAssignment(item.id).subscribe(assignment => {
            if (assignment) {
                this.assignments.unshift(assignment);
                this.snackBar.open('Assignment has been successfully copied!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
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
            this.snackBar.open(message ? message : 'Error occurred while copying assignment!', '', {
                duration: 3000,
                panelClass: ['error-snackbar']
            });
        });
    }

    onDeleteAssignment(assignment_id) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                // 'message': 'Are you sure that you want to remove? This will permanently delete the assignment.'
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.assignmentService.deleteAssignment(assignment_id)
                    .subscribe(response => {
                        this.assignments = this.assignments.filter( (item) => {
                            return item.id !== assignment_id;
                        });
                        this.snackBar.open('Assignment has been successfully deleted!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while deleting assignment!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
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
