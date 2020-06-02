import {Component, OnInit} from '@angular/core';
import {UserService} from '../../_services/user.service';
import {UnsubscribeDialogComponent} from './unsubscribe-dialog/unsubscribe-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';

@Component({
    selector: 'classes',
    templateUrl: './classes.component.html',
    styleUrls: ['./classes.component.scss'],
    providers: [UserService]
})
export class ClassesComponent implements OnInit {

    public myClasses = [];
    public availableClasses = [];
    public availableClassesData = [];
    public addClass = false;
    public nameFilter;
    public teacherFilter;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(
        private userService: UserService,
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
                this.availableClasses = this.availableClassesData = response['available_classes'];
            });
    }

    onUnsubscribe(class_id) {
        const unsubscribeDialogRef = this.dialog.open(UnsubscribeDialogComponent, {
            data: {},
            position: this.dialogPosition
        });
        unsubscribeDialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.userService.unsubscribeClass(class_id)
                    .subscribe(response => {
                        const current = this.myClasses.filter( (item) => {
                            return item.id === class_id;
                        });
                        this.availableClasses.push(current[0]);
                        this.availableClassesData = this.availableClasses;
                        this.myClasses = this.myClasses.filter( (item) => {
                            return item.id !== class_id;
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
                const current = this.availableClasses.filter( (item) => {
                    return item.id === class_id;
                });
                this.myClasses.push(current[0]);
                this.availableClasses = this.availableClasses.filter( (item) => {
                    return item.id !== class_id;
                });
                this.availableClassesData = this.availableClasses;
                this.addClass = false;
            });
    }

    filter() {
        if (this.nameFilter && this.teacherFilter) {
            this.availableClasses = this.availableClassesData.filter( (item) => {
                return item.name.toLowerCase().indexOf(this.nameFilter.toLowerCase()) !== -1
                    && item.teacher.toLowerCase().indexOf(this.teacherFilter.toLowerCase()) !== -1;
            });
        } else if (this.nameFilter) {
            this.availableClasses = this.availableClassesData.filter( (item) => {
                return item.name.toLowerCase().indexOf(this.nameFilter.toLowerCase()) !== -1;
            });
        } else if (this.teacherFilter) {
            this.availableClasses = this.availableClassesData.filter( (item) => {
                return item.teacher.toLowerCase().indexOf(this.teacherFilter.toLowerCase()) !== -1;
            });
        } else {
            this.availableClasses = this.availableClassesData;
        }
    }

}
