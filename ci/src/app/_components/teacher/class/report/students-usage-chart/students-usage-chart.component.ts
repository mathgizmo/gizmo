import {Component, OnInit, Input} from '@angular/core';
import {ClassesManagementService} from '../../../../../_services/index';
import * as moment from 'moment';

@Component({
    selector: 'app-students-usage-chart',
    templateUrl: './students-usage-chart.component.html',
    styleUrls: ['./students-usage-chart.component.scss'],
    providers: [ClassesManagementService]
})
export class StudentsUsageChartComponent implements OnInit {

    constructor(private classService: ClassesManagementService) {}
    @Input() classId: number;
    studentId = '';
    dateFrom = '';
    dateTo = '';
    dateNow = moment().format('YYYY-MM-DD');

    students = [];

    public barChartOptions: any = {
        scaleShowVerticalLines: false,
        responsive: true,
        scales: {
            yAxes : [{
                ticks : {
                    min : 0
                }
            }]
        }
    };
    public barChartColors: Array<any> = [
        {
            backgroundColor: 'rgba(105,159,177,0.2)',
            borderColor: 'rgba(105,159,177,1)',
            pointBackgroundColor: 'rgba(105,159,177,1)',
            pointBorderColor: '#fafafa',
            pointHoverBackgroundColor: '#fafafa',
            pointHoverBorderColor: 'rgba(105,159,177)'
        },
        {
            backgroundColor: 'rgba(77,20,96,0.3)',
            borderColor: 'rgba(77,20,96,1)',
            pointBackgroundColor: 'rgba(77,20,96,1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(77,20,96,1)'
        }
    ];
    public mbarChartLabels: string[] = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    public barChartData: any[] = [
        {data: [0, 0, 0, 0, 0, 0, 0], label: 'Correct Answers'},
        {data: [0, 0, 0, 0, 0, 0, 0], label: 'Attempted Questions'}
    ];

    ngOnInit() {
        this.getStatistics();
        this.classService.getStudents(this.classId)
            .subscribe(students => {
                this.students = students;
            });
    }

    public getStatistics() {
        this.classService.geAnswersStatistics(this.classId, this.studentId, this.dateFrom, this.dateTo)
            .subscribe(response => {
                const labels = [];
                const attempts = [];
                const correct = [];
                response.forEach(row => {
                    const dayOfWeek = moment(row.date, 'YYYY-MM-DD').format('ddd');
                    labels.push(dayOfWeek + ' ' + row.date);
                    attempts.push(row.attempts);
                    correct.push(row.correct);
                });
                this.barChartData = [
                    {
                        data: correct,
                        label: 'Correct Answers'
                    },
                    {
                        data: attempts,
                        label: 'Attempted Questions'
                    }
                ];
                this.mbarChartLabels = labels;
            });
    }

    public chartClicked(e: any): void {
        // console.log(e);
    }

    public chartHovered(e: any): void {
        // console.log(e);
    }

}
