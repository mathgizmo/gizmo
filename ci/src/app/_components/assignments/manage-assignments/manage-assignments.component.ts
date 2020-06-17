import {Component, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {AssignmentService} from '../../../_services/assignment.service';
import {EditAssignmentDialogComponent} from './edit-assignment-dialog/edit-assignment-dialog.component';
import {YesNoDialogComponent} from '../../classes/yes-no-dialog/yes-no-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';

@Component({
    selector: 'manage-assignments',
    templateUrl: './manage-assignments.component.html',
    styleUrls: ['./manage-assignments.component.scss'],
    providers: [AssignmentService]
})
export class ManageAssignmentsComponent implements OnInit {

    public assignments = [];
    public icons = [];
    public id: number;
    public name: string;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private assignmentService: AssignmentService, public dialog: MatDialog, private deviceService: DeviceDetectorService) {
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
                    data: { 'title': 'Add Assignment', 'icons': this.icons, 'tree': tree },
                    position: this.dialogPosition
                });
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
                        this.assignmentService.addAssignment(result)
                            .subscribe(item => {
                                if (item) {
                                    this.assignments.unshift(item);
                                }
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
                            //
                        });
                    }
                });
            });
    }

    onDeleteAssignment(assignment_id) {
        const dialogRef = this.dialog.open(YesNoDialogComponent, {
            data: { 'message': 'Are you sure that you want to delete the assignment?'},
            position: this.dialogPosition
        });
        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.assignmentService.deleteAssignment(assignment_id)
                    .subscribe(response => {
                        this.assignments = this.assignments.filter( (item) => {
                            return item.id !== assignment_id;
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

}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
