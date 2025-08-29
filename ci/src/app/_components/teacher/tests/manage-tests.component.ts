import {Component, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {TestService} from '../../../_services/test.service';
import {EditTestDialogComponent} from './edit-test-dialog/edit-test-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import {DomSanitizer} from '@angular/platform-browser';
import {environment} from '../../../../environments/environment';
import {ActivatedRoute} from '@angular/router';
import {DeleteConfirmationDialogComponent, YesNoDialogComponent} from '../../dialogs/index';
import {MatSnackBar} from '@angular/material/snack-bar';
import {User} from '../../../_models';
import {AuthenticationService, ShareService, UserService} from '../../../_services';
import {compare} from '../../../_helpers/compare.helper';

@Component({
    selector: 'app-manage-tests',
    templateUrl: './manage-tests.component.html',
    styleUrls: ['./manage-tests.component.scss'],
    providers: [TestService, ShareService]
})
export class ManageTestsComponent implements OnInit {
    public user: User;

    public tests = [];
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

    constructor(private route: ActivatedRoute, private testService: TestService, private sanitizer: DomSanitizer,
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
        this.beginLoad();
        this.userService.getProfile().subscribe((res: any) => {
            if (res && res.tags) { this.teacherTags = res.tags; }
            this.endLoad();
        }, _ => this.endLoad());
        this.checkNewShares();
    }

    checkNewShares() {
    this.beginLoad();
    this.shareService.getNewShare('test').subscribe(res => {
            if (res.item) {
                const dialogRef = this.dialog.open(YesNoDialogComponent, {
                    data: { 'message': `You have been sent<br> <b>${res.item.test.name}</b><br> by <b>${res.item.sender.email}</b><br>are you willing to accept it into your tests list?<br><br><div><small style="font-size: 70%">If you do not accept this test it will be removed from your list.</small><br><small style="font-size: 70%">If you accept the test, you can use it, remove it or modify it as you wish.</small></div><br>`,
                            'text_yes': 'Accept',
                            'text_no': 'Decline'
                    },
                    position: this.dialogPosition,
                    disableClose: true
                });
                dialogRef.afterClosed().subscribe(result => {
                    this.shareService.newShareToggle('test', res.item.item_id, result).subscribe(() => {
                        return this.checkNewShares();
                    });
                });
            } else {
                this.beginLoad();
                this.testService.getTests()
                    .subscribe(response => {
                        this.tests = response;
                        this.endLoad();
                    }, _ => this.endLoad());
                this.beginLoad();
                this.testService.getAvailableIcons()
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

    private beginLoad() { this._pendingLoads++; this.loading = true; }
    private endLoad() { if (this._pendingLoads > 0) { this._pendingLoads--; } this.loading = this._pendingLoads > 0; }

    onAddTest() {
        if (!this.teacherTags || this.teacherTags.length === 0) {
            this.snackBar.open('Please add at least one area of interest in Profile first.', '', { duration: 3000, panelClass: ['error-snackbar'] });
            return;
        }
        this.testService.getAppTree()
            .subscribe(tree => {
                const dialogRef = this.dialog.open(EditTestDialogComponent, {
                    data: { 'title': 'Create Test', 'icons': this.icons, 'tree': tree, 'tags': this.teacherTags },
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
                        this.testService.addTest(result)
                            .subscribe(item => {
                                if (item) {
                                    this.tests.unshift(item);
                                    this.snackBar.open('Test has been successfully created!', '', {
                                        duration: 3000,
                                        panelClass: ['success-snackbar']
                                    });
                                }
                            }, error => {
                                let message = '';
                                if (typeof error === 'object') {
                                    Object.values(error).forEach(x => { message += x + ' '; });
                                } else { message = error; }
                                this.snackBar.open(message ? message : 'Error occurred while creating test!', '', {
                                    duration: 3000,
                                    panelClass: ['error-snackbar']
                                });
                            });
                    }
                });
            });
    }

    onEditTest(item) {
        this.testService.getAppTree(item.id)
            .subscribe(tree => {
                const dialogRef = this.dialog.open(EditTestDialogComponent, {
                    data: { 'title': 'Edit Test', 'test': item, 'icons': this.icons, 'tree': tree, 'tags': this.teacherTags },
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
                        this.testService.updateTest(item.id, result).subscribe(res => {
                            this.snackBar.open('Test has been successfully updated!', '', {
                                duration: 3000,
                                panelClass: ['success-snackbar']
                            });
                        }, error => {
                            let message = '';
                            if (typeof error === 'object') {
                                Object.values(error).forEach(x => { message += x + ' '; });
                            } else { message = error; }
                            this.snackBar.open(message ? message : 'Error occurred while updating test!', '', {
                                duration: 3000,
                                panelClass: ['error-snackbar']
                            });
                        });
                    }
                });
            });
    }

    onCopyTest(item) {
        this.testService.copyTest(item.id).subscribe(test => {
            if (test) {
                this.tests.unshift(test);
                this.snackBar.open('Test has been successfully copied!', '', {
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
            this.snackBar.open(message ? message : 'Error occurred while copying test!', '', {
                duration: 3000,
                panelClass: ['error-snackbar']
            });
        });
    }

    onDeleteTest(test_id) {
        const dialogRef = this.dialog.open(DeleteConfirmationDialogComponent, {
            data: {
                // 'message': 'Are you sure that you want to remove? This will permanently delete the test.'
            },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.testService.deleteTest(test_id)
                    .subscribe(response => {
                        this.tests = this.tests.filter( (item) => {
                            return item.id !== test_id;
                        });
                        this.snackBar.open('Test has been successfully deleted!', '', {
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
                        this.snackBar.open(message ? message : 'Error occurred while deleting test!', '', {
                            duration: 3000,
                            panelClass: ['error-snackbar']
                        });
                    });
            }
        });
    }

    sortData(sort: Sort) {
        const data = this.tests.slice();
        if (!sort.active || sort.direction === '') {
            this.tests = data;
            return;
        }
        this.tests = data.sort((a, b) => {
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
