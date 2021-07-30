import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {ClassesManagementService} from '../../../../_services';

@Component({
    selector: 'app-class-report',
    templateUrl: './class-report.component.html',
    styleUrls: ['./class-report.component.scss']
})
export class ClassReportComponent implements OnInit {

    public classId: number;
    public class = {
        id: 0,
        name: ''
    };
    public assignment_students;
    public assignments;
    public tests;
    public students;

    public backLinkText = 'Back';

    private sub: any;

    public reportShow = [
        true,
        true,
        true,
        true
    ];

    constructor(private route: ActivatedRoute, private classService: ClassesManagementService) {}

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
        });
        this.classService.getReport(this.classId)
            .subscribe(response => {
                this.class = response.class;
                this.assignment_students = response.students;
                this.assignments = response.assignments;
                this.tests = response.tests;
                this.students = response.class_students;
                this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Report';
            });
    }

    slideToggle(index: any) {
        $('#report' + index + ' > .report-data').slideToggle('slow');
        this.reportShow[index] = !this.reportShow[index];
    }
}
