import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {ClassesManagementService} from '../../../../_services';
import {Sort} from "@angular/material/sort";

@Component({
    selector: 'app-class-report',
    templateUrl: './class-report.component.html',
    styleUrls: ['./class-report.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassReportComponent implements OnInit {

    public class_id: number;
    public class = {
        id: 0,
        name: ''
    };
    public assignments;
    public students;

    public backLinkText = 'Back';

    private sub: any;

    constructor(private route: ActivatedRoute, private classService: ClassesManagementService) {}

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.class_id = +params['class_id'];
        });
        this.classService.getReport(this.class_id)
            .subscribe(response => {
                this.class = response.class;
                this.assignments = response.assignments;
                this.students = response.students;
                this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.class_id) + ' > Report';
            });
    }

    sortData(sort: Sort) {
        const data = this.students.slice();
        if (!sort.active || sort.direction === '') {
            this.students = data;
            return;
        }
        this.students = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'name': return compare(a.name, b.name, isAsc);
                default: return 0;
            }
        });
    }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    if (typeof a === 'string' || typeof b === 'string') {
        a = ('' + a).toLowerCase();
        b = ('' + b).toLowerCase();
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
