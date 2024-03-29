import {Component, OnInit, Input, Inject, NgZone, PLATFORM_ID, OnDestroy, AfterViewInit} from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import {AuthenticationService, ClassesManagementService} from '../../../../../_services';

import * as am4core from '@amcharts/amcharts4/core';
import * as am4charts from '@amcharts/amcharts4/charts';
import am4themes_animated from '@amcharts/amcharts4/themes/animated';
import * as moment from 'moment';

@Component({
    selector: 'app-tests-students-usage-chart',
    templateUrl: './tests-students-usage-chart.component.html',
    styleUrls: ['./tests-students-usage-chart.component.scss']
})
export class TestsStudentsUsageChartComponent implements OnInit, AfterViewInit, OnDestroy {

    @Input() classId: number;
    @Input() tests = [];
    @Input() forStudent = false;
    @Input() forResearch = false;

    public studentId = '';
    public appId = '';
    public dateFrom = '';
    public dateTo = '';
    public dateNow = moment().format('YYYY-MM-DD');

    public students = [];
    public availableStudents = [];

    private chart: am4charts.XYChart;

    constructor(
        @Inject(PLATFORM_ID) private platformId,
        private zone: NgZone,
        private classService: ClassesManagementService,
        private authenticationService: AuthenticationService
    ) {}

    browserOnly(f: () => void) {
        if (isPlatformBrowser(this.platformId)) {
            this.zone.runOutsideAngular(() => {
                f();
            });
        }
    }

    ngOnInit() {
        if (this.forStudent) {
            const user = this.authenticationService.userValue;
            this.studentId = user.user_id + '';
        } else {
            const filters = this.forResearch ? { for_research: 1 } : null;
            this.classService.getStudents(this.classId, false, filters)
                .subscribe(students => {
                    this.students = students;
                    this.availableStudents = students;
                });
        }
        if (!this.tests) {
            this.classService.getTests(this.classId)
                .subscribe(tests => {
                    this.tests = tests.tests;
                });
        }
    }

    ngAfterViewInit() {
        this.getStatistics();
    }

    ngOnDestroy() {
        this.browserOnly(() => {
            if (this.chart) {
                this.chart.dispose();
            }
        });
    }

    public getStatistics() {
        this.classService.getAnswersStatistics(this.classId, this.studentId, this.appId,
            this.dateFrom, this.dateTo, 'test', this.forResearch)
            .subscribe(response => {
                // build chart
                this.browserOnly(() => {
                    am4core.useTheme(am4themes_animated);
                    const chart = am4core.create('tests-usage-chart', am4charts.XYChart);
                    chart.data = [];
                    response.forEach(row => {
                        // const dayOfWeek = moment(row.date, 'YYYY-MM-DD').format('ddd');
                        chart.data.push({
                            'date': row.date, // dayOfWeek + ' ' + row.date,
                            'attempts': row.attempts,
                            'correct': row.correct,
                        });
                    });
                    const dateAxis = chart.xAxes.push(new am4charts.DateAxis());
                    dateAxis.renderer.grid.template.location = 0;
                    const valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                    valueAxis.tooltip.disabled = true;
                    valueAxis.renderer.minWidth = 35;
                    valueAxis.min = 0;
                    const series = chart.series.push(new am4charts.LineSeries());
                    series.name = 'Correct Answers';
                    series.dataFields.dateX = 'date';
                    series.dataFields.valueY = 'correct';
                    series.tooltipText = 'Correct Answers: {valueY.value}';
                    series.fillOpacity = 0.3;
                    series.strokeWidth = 2;
                    series.stroke = am4core.color('#5ac18e');
                    series.fill = am4core.color('#5ac18e');
                    const series2 = chart.series.push(new am4charts.LineSeries());
                    series2.name = 'Attempted Questions';
                    series2.dataFields.dateX = 'date';
                    series2.dataFields.valueY = 'attempts';
                    series2.tooltipText = 'Attempted Questions: {valueY.value}';
                    series2.sequencedInterpolation = true;
                    series2.fillOpacity = 0.3;
                    series2.defaultState.transitionDuration = 1000;
                    series2.strokeWidth = 2;
                    chart.cursor = new am4charts.XYCursor();
                    const scrollbarX = new am4charts.XYChartScrollbar();
                    scrollbarX.series.push(series);
                    scrollbarX.series.push(series2);
                    chart.scrollbarX = scrollbarX;
                    chart.legend = new am4charts.Legend();
                    chart.legend.position = 'bottom';
                    this.chart = chart;
                });
            });
    }

    public filterStudents(event) {
        if (!event) {
            this.availableStudents = this.students;
        }
        if (typeof event === 'string') {
            this.availableStudents = this.students.filter(a => a.email.toLowerCase()
                .startsWith(event.toLowerCase()));
        }
    }

}
