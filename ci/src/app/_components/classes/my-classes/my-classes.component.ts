import {Component, OnInit} from '@angular/core';
import {UserService} from '../../../_services/user.service';
import {YesNoDialogComponent} from '../yes-no-dialog/yes-no-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';

@Component({
    selector: 'my-classes',
    templateUrl: './my-classes.component.html',
    styleUrls: ['./my-classes.component.scss'],
    providers: [UserService]
})
export class MyClassesComponent implements OnInit {

    public myClasses = [];
    public availableClasses = [];
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
                this.availableClasses = response['available_classes'];
            });
    }

    onUnsubscribe(class_id) {
        const dialogRef = this.dialog.open(YesNoDialogComponent, {
            data: { 'message': 'Are you sure that you want to unsubscribe?'},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.userService.unsubscribeClass(class_id)
                    .subscribe(response => {
                        const current = this.myClasses.filter( (item) => {
                            return item.id === class_id;
                        });
                        this.availableClasses.push(current[0]);
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
                this.addClass = false;
            });
    }

}
