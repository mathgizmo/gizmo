import {Component, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {TestService} from '../../../_services/test.service';
import {EditTestDialogComponent} from './edit-test-dialog/edit-test-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import {DomSanitizer} from '@angular/platform-browser';
import {environment} from '../../../../environments/environment';
import {ActivatedRoute} from '@angular/router';
import {DeleteConfirmationDialogComponent} from '../../dialogs/index';
import {MatSnackBar} from '@angular/material/snack-bar';
import {compare} from '../../../_helpers/compare.helper';

@Component({
    selector: 'app-manage-tests',
    templateUrl: './manage-tests.component.html',
    styleUrls: ['./manage-tests.component.scss'],
    providers: [TestService]
})
export class ManageTestsComponent implements OnInit {

    public tests = [];
    public icons = [];
    public name: string;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    private readonly adminUrl = environment.adminUrl;

    constructor(private route: ActivatedRoute, private testService: TestService, private sanitizer: DomSanitizer,
                public dialog: MatDialog, private deviceService: DeviceDetectorService, public snackBar: MatSnackBar) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.testService.getTests()
            .subscribe(response => {
                this.tests = response;
            });
        this.testService.getAvailableIcons()
            .subscribe(response => {
                this.icons = response;
            });
    }

    onAddTest() {
        this.testService.getAppTree()
            .subscribe(tree => {
                const dialogRef = this.dialog.open(EditTestDialogComponent, {
                    data: { 'title': 'Create Test', 'icons': this.icons, 'tree': tree },
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
                        this.testService.addTest(result)
                            .subscribe(item => {
                                if (item) {
                                    this.tests.unshift(item);
                                    this.snackBar.open('Test have been successfully created!', '', {
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
                    data: { 'title': 'Edit Test', 'test': item, 'icons': this.icons, 'tree': tree },
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
                        this.testService.updateTest(item.id, result).subscribe(res => {
                            this.snackBar.open('Test have been successfully updated!', '', {
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
                this.snackBar.open('Test have been successfully copied!', '', {
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
                        this.snackBar.open('Test have been successfully deleted!', '', {
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
