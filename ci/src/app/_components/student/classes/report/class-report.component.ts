import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {ClassesManagementService} from '../../../../_services';
import {MatDialog} from '@angular/material/dialog';
import {ResearchConsentDialogComponent} from '../research-consent-dialog/research-consent-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';

@Component({
    selector: 'app-my-class-report',
    templateUrl: './class-report.component.html',
    styleUrls: ['./class-report.component.scss']
})
export class MyClassReportComponent implements OnInit {

    public classId: number;
    public myClass = {
        id: 0,
        name: '',
        is_researchable: 0,
        student_data: {
            is_consent_read: 0,
            is_element1_accepted: 0,
            is_element2_accepted: 0,
            is_element3_accepted: 0,
            is_element4_accepted: 0
        }
    };
    public assignments;
    public students;
    public tests;
    public class_students;

    public backLinkText = 'Back';

    private sub: any;

    public reportShow = [
        true,
        true,
        true,
        true
    ];

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private route: ActivatedRoute,
                private classService: ClassesManagementService,
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
            this.classService.getReport(this.classId)
                .subscribe(response => {
                    this.myClass = response.class;
                    this.assignments = response.assignments;
                    this.students = response.students;
                    this.tests = response.tests;
                    this.class_students = response.class_students;
                    this.backLinkText = 'My Classes > ' + (this.myClass ? this.myClass.name : this.classId) + ' > Report';
                    if (this.myClass && this.myClass.is_researchable && this.myClass.student_data
                        && !this.myClass.student_data.is_consent_read) {
                        this.dialog.open(ResearchConsentDialogComponent, {
                            data: { 'class_id': this.classId, 'consent': this.myClass.student_data },
                            position: this.dialogPosition
                        });
                    }
                });
        });
    }

    slideToggle(index: any) {
        $('#report' + index + ' > .report-data').slideToggle('slow');
        this.reportShow[index] = !this.reportShow[index];
    }
}
