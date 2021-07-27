import {Component, OnInit} from '@angular/core';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import {Sort} from '@angular/material/sort';
import {MatSnackBar} from '@angular/material/snack-bar';
import {UserService, ClassesManagementService} from '../../../_services';
import {InfoDialogComponent, YesNoDialogComponent} from '../../dialogs';
import {compare} from '../../../_helpers/compare.helper';

@Component({
    selector: 'app-my-classes',
    templateUrl: './classes.component.html',
    styleUrls: ['./classes.component.scss'],
    providers: [UserService, ClassesManagementService]
})
export class MyClassesComponent implements OnInit {

    public myClasses = [];
    public availableClasses = [];
    public addClass = false;
    public idFilter;
    public nameFilter;
    public teacherFilter;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(
        private classService: ClassesManagementService,
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
        this.userService.getClasses()
            .subscribe(response => {
                this.myClasses = response['my_classes'];
                this.availableClasses = response['available_classes'];
            });
    }

    onUnsubscribe(class_id) {
        const dialogRef = this.dialog.open(YesNoDialogComponent, {
            data: {'message': 'Are you sure that you want to unsubscribe?'},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.userService.unsubscribeClass(class_id)
                    .subscribe(response => {
                        const current = this.myClasses.filter((item) => {
                            return item.id === class_id;
                        });
                        this.availableClasses.push(current[0]);
                        this.myClasses = this.myClasses.filter((item) => {
                            return item.id !== class_id;
                        });
                        this.dialog.open(InfoDialogComponent, {
                            data: {'message': 'You have been successfully unsubscribed!<br\>Please email your teacher to ensure that your information is deleted from class records.'},
                            position: this.dialogPosition
                        });
                    });
            }
        });
    }

    onAddClass() {
        this.addClass = true;
    }

    onSubscribe(class_id) {
        this.userService.subscribeClass(class_id)
            .subscribe(response => {
                const current = this.availableClasses.filter((item) => {
                    return item.id === class_id;
                });
                this.myClasses.push(current[0]);
                this.availableClasses = this.availableClasses.filter((item) => {
                    return item.id !== class_id;
                });
                this.addClass = false;
            });
    }

    sortMyClasses(sort: Sort) {
        const data = this.myClasses.slice();
        if (!sort.active || sort.direction === '') {
            this.myClasses = data;
            return;
        }
        this.myClasses = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id':
                    return compare(a.id, b.id, isAsc);
                case 'name':
                    return compare(a.name, b.name, isAsc);
                case 'teacher':
                    return compare(a.teacher, b.teacher, isAsc);
                default:
                    return 0;
            }
        });
    }

    sortAvailableClasses(sort: Sort) {
        const data = this.availableClasses.slice();
        if (!sort.active || sort.direction === '') {
            this.availableClasses = data;
            return;
        }
        this.availableClasses = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id':
                    return compare(a.id, b.id, isAsc);
                case 'name':
                    return compare(a.name, b.name, isAsc);
                case 'teacher':
                    return compare(a.teacher, b.teacher, isAsc);
                default:
                    return 0;
            }
        });
    }
}
