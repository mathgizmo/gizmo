import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {ResearchConsentDialogComponent} from '../research-consent-dialog/research-consent-dialog.component';
import {UserService, SettingsService} from '../../../../_services';
import {MatDialog} from '@angular/material/dialog';
import {DeviceDetectorService} from 'ngx-device-detector';

@Component({
    selector: 'app-my-class-research-status',
    templateUrl: './class-research-status.component.html',
    styleUrls: ['./class-research-status.component.scss'],
    providers: [SettingsService]
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
    public consentMessage = `The developers of the <strong>HNP webapp</strong> with support from the <a href="https://heqco.ca/" target="_blank"><strong>Higher Education Quality Council of Ontario</strong></a> along with <a href="https://www.georgebrown.ca/" target="_blank"><strong>George Brown College</strong></a> and <a href="https://www.mcmaster.ca/" target="_blank"><strong>McMaster university</strong></a> are inviting you to help evaluate it as a useful educational tool for health numeracy.
          <br/>
          Your teacher has integrated the HNP webapp into your course materials and you will have access to the application whether you participate or not, thus participation is voluntary. Your teacher will not know whether you will participate or not, and we aim to evaluate the application not the research participants.
          <br/>
          We would like to invite you to take part in one or more of 4 formats in which we will be using to evaluate the app. They are described at length in the letter of informed consent that you need to read. You can have access to it here. Three of them involve compensation to offset time and any costs you incur, and one does not require you to do anything except provide access to data. lease read carefully
          <br/>
          To get more information about the study please email <strong>Taras Gula</strong> at <a href="mailto:tgula@georgebrown.ca">tgula@georgebrown.ca</a> or <strong>Miroslav Lovric</strong> at <a href="mailto:lovric@mcmaster.ca">lovric@mcmaster.ca</a>.`;

    public backLinkText = 'Back';

    private sub: any;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private route: ActivatedRoute,
                private userService: UserService,
                private settingsService: SettingsService,
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
        this.settingsService.getSetting('research_consent')
            .subscribe(result => {
                if (result && result.value) {
                    this.consentMessage = result.value;
                }
            });
    }
}
