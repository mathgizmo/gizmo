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
import {UserService} from '../../../_services/user.service';

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
    public teacherTags: any[] = [];
    public loading = false;
    private _pendingLoads = 0;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    private readonly adminUrl = environment.adminUrl;

    constructor(private route: ActivatedRoute, private assignmentService: AssignmentService, private sanitizer: DomSanitizer,
                private authenticationService: AuthenticationService,
                private shareService: ShareService,
                public dialog: MatDialog, private deviceService: DeviceDetectorService, public snackBar: MatSnackBar,
                private userService: UserService) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.user = this.authenticationService.userValue;
        // load teacher tags
        this.beginLoad();
        this.userService.getProfile().subscribe((res: any) => {
            if (res && res.tags) {
                this.teacherTags = res.tags;
            }
            this.endLoad();
        }, _ => this.endLoad());
        this.checkNewShares();
    }

    checkNewShares() {
    this.beginLoad();
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
                this.beginLoad();
                this.assignmentService.getAssignments()
                    .subscribe(response => {
                        this.assignments = response;
                        this.endLoad();
                    }, _ => this.endLoad());
                this.beginLoad();
                this.assignmentService.getAvailableIcons()
                    .subscribe(response => {
                        this.icons = response;
                        this.endLoad();
                    }, _ => this.endLoad());
            }
            this.endLoad();
        }, _ => this.endLoad());
    }

    getTagNames(item: any): string {
        const tags = (item as any)?.tags as any[];
        return tags && tags.length ? tags.map(t => t.name).join(', ') : '';
    }

    onAddAssignment() {
        // ensure tags present
        if (!this.teacherTags || this.teacherTags.length === 0) {
            this.snackBar.open('Please add at least one area of interest in Profile first.', '', { duration: 3000, panelClass: ['error-snackbar'] });
            return;
        }
    this.beginLoad();
    this.assignmentService.getAppTree()
            .subscribe(tree => {
                const dialogRef = this.dialog.open(EditAssignmentDialogComponent, {
                    data: { 'title': 'Create Assignment', 'icons': this.icons, 'tree': tree, 'tags': this.teacherTags },
                    position: this.dialogPosition
                });
        this.endLoad();
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
            this.beginLoad();
                        this.assignmentService.addAssignment(result)
                            .subscribe(item => {
                                if (item) {
                                    this.assignments.unshift(item);
                                    this.snackBar.open('Assignment has been successfully created!', '', {
                                        duration: 3000,
                                        panelClass: ['success-snackbar']
                                    });
                                }
                this.endLoad();
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
                this.endLoad();
                            });
                    }
                });
        }, _ => this.endLoad());
    }

    onEditAssignment(item) {
    this.beginLoad();
    this.assignmentService.getAppTree(item.id)
            .subscribe(tree => {
                const dialogRef = this.dialog.open(EditAssignmentDialogComponent, {
                    data: { 'title': 'Edit Assignment', 'assignment': item, 'icons': this.icons, 'tree': tree, 'tags': this.teacherTags },
                    position: this.dialogPosition
                });
        this.endLoad();
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
            this.beginLoad();
                        this.assignmentService.updateAssignment(item.id, result).subscribe(res => {
                            // update local list so new tags and fields are visible immediately
                            const idx = this.assignments.findIndex((a: any) => a.id === res.id);
                            if (idx >= 0) { this.assignments[idx] = res; }
                            this.snackBar.open('Assignment has been successfully updated!', '', {
                                duration: 3000,
                                panelClass: ['success-snackbar']
                            });
                this.endLoad();
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
                this.endLoad();
                        });
                    }
                });
        }, _ => this.endLoad());
    }

    onCopyAssignment(item) {
        this.beginLoad();
        this.assignmentService.copyAssignment(item.id).subscribe(assignment => {
            if (assignment) {
                this.assignments.unshift(assignment);
                this.snackBar.open('Assignment has been successfully copied!', '', {
                    duration: 3000,
                    panelClass: ['success-snackbar']
                });
            }
            this.endLoad();
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
            this.endLoad();
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
        this.beginLoad();
                this.assignmentService.deleteAssignment(assignment_id)
                    .subscribe(response => {
                        this.assignments = this.assignments.filter( (item) => {
                            return item.id !== assignment_id;
                        });
                        this.snackBar.open('Assignment has been successfully deleted!', '', {
                            duration: 3000,
                            panelClass: ['success-snackbar']
                        });
            this.endLoad();
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
            this.endLoad();
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

    private beginLoad() {
        this._pendingLoads++;
        if (this._pendingLoads === 1) { this.loading = true; }
    }

    private endLoad() {
        this._pendingLoads = Math.max(0, this._pendingLoads - 1);
        if (this._pendingLoads === 0) { this.loading = false; }
    }

}
