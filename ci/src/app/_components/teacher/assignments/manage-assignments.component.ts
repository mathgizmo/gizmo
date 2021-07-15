import {Component, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {AssignmentService} from '../../../_services/assignment.service';
import {EditAssignmentDialogComponent} from './edit-assignment-dialog/edit-assignment-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import {DomSanitizer} from '@angular/platform-browser';
import {environment} from '../../../../environments/environment';
import {ActivatedRoute} from '@angular/router';
import {DeleteConfirmationDialogComponent} from '../../dialogs/index';
import {MatSnackBar} from '@angular/material/snack-bar';
import {compare} from '../../../_helpers/compare.helper';

@Component({
    selector: 'app-manage-assignments',
    templateUrl: './manage-assignments.component.html',
    styleUrls: ['./manage-assignments.component.scss'],
    providers: [AssignmentService]
})
export class ManageAssignmentsComponent implements OnInit {

    public assignments = [];
    public icons = [];
    public name: string;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    private readonly adminUrl = environment.adminUrl;

    constructor(private route: ActivatedRoute, private assignmentService: AssignmentService, private sanitizer: DomSanitizer,
                public dialog: MatDialog, private deviceService: DeviceDetectorService, public snackBar: MatSnackBar) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.assignmentService.getAssignments()
            .subscribe(response => {
                this.assignments = response;
            });
        this.assignmentService.getAvailableIcons()
            .subscribe(response => {
                this.icons = response;
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
