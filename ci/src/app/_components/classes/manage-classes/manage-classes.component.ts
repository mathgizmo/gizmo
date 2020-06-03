import {Component, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {ClassesManagementService} from '../../../_services/classes-management.service';
import {EditClassDialogComponent} from './edit-class-dialog/edit-class-dialog.component';
import {YesNoDialogComponent} from '../yes-no-dialog/yes-no-dialog.component';
import {ClassStudentsDialogComponent} from './class-students-dialog/class-students-dialog.component';
import {ClassAssignmentsDialogComponent} from './class-assignments-dialog/class-assignments-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';

@Component({
    selector: 'manage-classes',
    templateUrl: './manage-classes.component.html',
    styleUrls: ['./manage-classes.component.scss'],
    providers: [ClassesManagementService]
})
export class ManageClassesComponent implements OnInit {

    public classes = [];
    public id: number;
    public name: string;
    public subscription_type: string;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private classService: ClassesManagementService, public dialog: MatDialog, private deviceService: DeviceDetectorService) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.classService.getClasses()
            .subscribe(response => {
                this.classes = response;
            });
    }

    onAddClass() {
        const dialogRef = this.dialog.open(EditClassDialogComponent, {
            data: { 'title': 'Add Class' },
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.addClass(result)
                    .subscribe(item => {
                        this.classes.unshift(item);
                    });
            }
        });
    }

    onEditClass(item) {
        const dialogRef = this.dialog.open(EditClassDialogComponent, {
            data: { 'title': 'Edit Class', 'class': item},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.updateClass(item.id, result).subscribe(res => {
                    //
                });
            }
        });
    }

    onDeleteClass(class_id) {
        const dialogRef = this.dialog.open(YesNoDialogComponent, {
            data: { 'message': 'Are you sure that you want to delete the class?'},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.classService.deleteClass(class_id)
                    .subscribe(response => {
                        this.classes = this.classes.filter( (item) => {
                            return item.id !== class_id;
                        });
                    });
            }
        });
    }

    onManageStudents(item) {
        this.classService.getStudents(item.id)
            .subscribe(students => {
                const dialogRef = this.dialog.open(ClassStudentsDialogComponent, {
                    data: { 'students': students, 'class': item},
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(result => {});
            });
    }

    onManageAssignments(item) {
        this.classService.getAssignments(item.id)
            .subscribe(res => {
                const dialogRef = this.dialog.open(ClassAssignmentsDialogComponent, {
                    data: { 'assignments': res['assignments'], 'available_assignments': res['available_assignments'], 'class': item},
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(result => {});
            });
    }

    sortData(sort: Sort) {
        const data = this.classes.slice();
        if (!sort.active || sort.direction === '') {
            this.classes = data;
            return;
        }
        this.classes = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'subscription_type': return compare(a.subscription_type, b.subscription_type, isAsc);
                default: return 0;
            }
        });
    }

}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
