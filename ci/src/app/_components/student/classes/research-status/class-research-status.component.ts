import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, } from '@angular/router';
import {ResearchConsentDialogComponent} from '../research-consent-dialog/research-consent-dialog.component';
import { UserService} from '../../../../_services';
import {MatDialog} from '@angular/material/dialog';
import {DeviceDetectorService} from 'ngx-device-detector';

@Component({
    selector: 'app-my-class-research-status',
    templateUrl: './class-research-status.component.html',
    styleUrls: ['./class-research-status.component.scss']
})
export class MyClassResearchStatusComponent implements OnInit {

    public classId: number;
    public myClass = {
        id: 0,
        name: '',
        is_researchable: 0,
        pivot: {
            is_consent_read: 0,
            is_element1_accepted: 0,
            is_element2_accepted: 0,
            is_element3_accepted: 0,
            is_element4_accepted: 0
        }
    };

    public backLinkText = 'Back';

    private sub: any;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private route: ActivatedRoute,
                private userService: UserService,
                public dialog: MatDialog,
                private deviceService: DeviceDetectorService
    ) {
        this.dialogPosition = {bottom: '18vh'};
        if (this.isMobile || this.isTablet) {
            this.dialogPosition = {bottom: '2vh'};
        }
    }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            this.userService.getClass(this.classId)
                .subscribe(response => {
                    this.myClass = response;
                    this.backLinkText = 'My Classes > ' + (this.myClass ? this.myClass.name : this.classId) + ' > Research status';
                    if (this.myClass && this.myClass.is_researchable && this.myClass.pivot && !this.myClass.pivot.is_consent_read) {
                        this.dialog.open(ResearchConsentDialogComponent, {
                            data: { 'class_id': this.classId, 'consent': this.myClass.pivot },
                            position: this.dialogPosition
                        });
                    }
                });
        });
    }
}
