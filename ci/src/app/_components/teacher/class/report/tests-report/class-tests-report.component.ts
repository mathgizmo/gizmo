import {Component, Input, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {environment} from '../../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {TestReportDialogComponent} from '../../tests/test-report-dialog/test-report-dialog.component';
import {DeviceDetectorService} from 'ngx-device-detector';
import {MatDialog} from '@angular/material/dialog';
import * as moment from 'moment/moment';

@Component({
    selector: 'app-class-tests-report',
    templateUrl: './class-tests-report.component.html',
    styleUrls: ['./class-tests-report.component.scss'],
    providers: []
})
export class ClassTestsReportComponent implements OnInit {

    @Input() tests;
    @Input() classId: number;
    @Input() forStudent = false;

    private readonly adminUrl = environment.adminUrl;

    dialogPosition: any;
    private isMobile = this.deviceService.isMobile();
    private isTablet = this.deviceService.isTablet();
    private isDesktop = this.deviceService.isDesktop();

    constructor(private sanitizer: DomSanitizer,
                private deviceService: DeviceDetectorService,
                public dialog: MatDialog
    ) {}

    ngOnInit() {}

    onShowTestReport(item) {
        const dialogRef = this.dialog.open(TestReportDialogComponent, {
            data: {
                title: item.name + ': report',
                test: item
            },
            position: this.dialogPosition
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
                case 'start_at': return compare(a.start_at, b.start_at, isAsc, true);
                case 'due_at': return compare(a.due_at, b.due_at, isAsc, true);
                case 'attempts': return compare(a.attempts, b.attempts, isAsc);
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

function compare(a: number | string, b: number | string, isAsc: boolean, isDate = false) {
    if (isDate) {
        const aDate = moment(a, 'YYYY-MM-DD hh:mm A');
        const bDate = moment(b, 'YYYY-MM-DD hh:mm A');
        return (aDate.isBefore(bDate) ? -1 : 1) * (isAsc ? 1 : -1);
    } else {
        if (typeof a === 'string' || typeof b === 'string') {
            a = ('' + a).toLowerCase();
            b = ('' + b).toLowerCase();
        }
        return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
    }
}
