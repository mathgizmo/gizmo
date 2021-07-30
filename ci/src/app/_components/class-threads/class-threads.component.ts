import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {AuthenticationService, ClassThreadsService} from '../../_services';
import {EditThreadDialogComponent} from './edit-thread-dialog/edit-thread-dialog.component';
import {DomSanitizer} from '@angular/platform-browser';
import {MatDialog} from '@angular/material/dialog';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatSnackBar} from '@angular/material/snack-bar';
import {DeleteConfirmationDialogComponent} from '../dialogs';
import {User} from '../../_models';

@Component({
    selector: 'app-class-threads',
    templateUrl: './class-threads.component.html',
    styleUrls: ['./class-threads.component.scss'],
    providers: [ClassThreadsService]
})
export class ClassThreadsComponent implements OnInit {

    public user: User;
    public classId: number;
    public class = {
        id: 0,
        name: ''
    };
    public threads = [];
    public activeThread;
    public replies = [];

    public backLinkText = 'Back';

    private sub: any;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private route: ActivatedRoute,
                private threadService: ClassThreadsService,
                private authenticationService: AuthenticationService,
                private sanitizer: DomSanitizer,
                public dialog: MatDialog,
                private deviceService: DeviceDetectorService,
                public snackBar: MatSnackBar) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.user = this.authenticationService.userValue;
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
        });
        this.threadService.getThreads(this.classId)
            .subscribe(response => {
                this.class = response.class;
                this.threads = response.items;
                this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Discussion';
            });
    }

    public openThread(thread) {
        this.threadService.getThreadReplies(this.classId, thread.id)
            .subscribe(response => {
                this.replies = response.items;
                this.activeThread = thread;
            });
    }

    public onCreateThread() {
        const dialogRef = this.dialog.open(EditThreadDialogComponent, {
            data: { 'title': 'Create Thread' },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.threadService.addThread(this.classId, result.title, result.message)
                    .subscribe(item => {
                        if (item) {
                            this.threads.unshift(item);
                            this.snackBar.open('Thread has been successfully created!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while creating thread!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    public onEditThread(thread) {
        const dialogRef = this.dialog.open(EditThreadDialogComponent, {
            data: { 'title': 'Edit Thread', 'thread': thread },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.threadService.updateThread(this.classId, thread.id, result.title, result.message).subscribe(res => {
                    this.snackBar.open('Thread has been successfully updated!', '', {
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
                    this.snackBar.open(message ? message : 'Error occurred while updating thread!', '', {
                        duration: 3000,
                        panelClass: ['error-snackbar']
                    });
                });
            }
        });
    }

    public onDeleteThread(thread) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                'message': 'Are you sure that you want to remove? This will permanently delete the thread.'
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.threadService.deleteThread(this.classId, thread.id)
                    .subscribe(response => {
                        this.threads = this.threads.filter( (item) => {
                            return item.id !== thread.id;
                        });
                        this.activeThread = null;
                        this.replies = [];
                        this.snackBar.open('Thread has been successfully deleted!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while deleting thread!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    public onCreateReply() {
        const dialogRef = this.dialog.open(EditThreadDialogComponent, {
            data: { 'title': 'Add Answer', hide_title: true },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.threadService.addThreadReply(this.classId, this.activeThread.id, result.message)
                    .subscribe(item => {
                        if (item) {
                            this.replies.unshift(item);
                            this.activeThread.replies_count++;
                            this.snackBar.open('Answer has been successfully created!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while creating answer!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    public onEditReply(reply) {
        const dialogRef = this.dialog.open(EditThreadDialogComponent, {
            data: { title: 'Edit Answer', thread: reply, hide_title: true },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.threadService.updateThreadReply(this.classId, reply.thread_id, reply.id, result.message).subscribe(res => {
                    this.snackBar.open('Answer has been successfully updated!', '', {
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
                    this.snackBar.open(message ? message : 'Error occurred while updating answer!', '', {
                        duration: 3000,
                        panelClass: ['error-snackbar']
                    });
                });
            }
        });
    }

    public onDeleteReply(reply) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                'message': 'Are you sure that you want to remove? This will permanently delete the answer.'
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.threadService.deleteThreadReply(this.classId, reply.thread_id, reply.id)
                    .subscribe(response => {
                        this.replies = this.replies.filter( (item) => {
                            return item.id !== reply.id;
                        });
                        this.activeThread.replies_count--;
                        this.snackBar.open('Answer has been successfully deleted!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while deleting answer!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }
}
