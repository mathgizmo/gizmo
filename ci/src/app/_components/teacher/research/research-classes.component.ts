import {Component, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import {MatSnackBar} from '@angular/material/snack-bar';
import {Router} from '@angular/router';
import {ClassesManagementService, AuthenticationService} from '../../../_services';
import {User} from '../../../_models';
import {compare} from '../../../_helpers/compare.helper';

@Component({
    selector: 'app-research-classes',
    templateUrl: './research-classes.component.html',
    styleUrls: ['./research-classes.component.scss']
})
export class ResearchClassesComponent implements OnInit {

    public user: User;
    public classes = [];
    public id: number;
    public name: string;
    public subscription_type: string;
    public class_type: string;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private classService: ClassesManagementService,
                private authenticationService: AuthenticationService,
                public dialog: MatDialog,
                private router: Router,
                private deviceService: DeviceDetectorService,
                public snackBar: MatSnackBar) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.user = this.authenticationService.userValue;
        this.classService.getResearchClasses()
            .subscribe(response => {
                this.classes = response;
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
                case 'class_type': return compare(a.class_type, b.class_type, isAsc);
                case 'subscription_type': return compare(a.subscription_type, b.subscription_type, isAsc);
                default: return 0;
            }
        });
    }

}


