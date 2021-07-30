import {Component, Input, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {compare} from '../../../../../_helpers/compare.helper';

@Component({
    selector: 'app-class-assignments-report',
    templateUrl: './class-assignments-report.component.html',
    styleUrls: ['./class-assignments-report.component.scss']
})
export class ClassAssignmentsReportComponent implements OnInit {

    @Input() assignments;
    @Input() students;
    @Input() forStudent = false;

    constructor() {}

    ngOnInit() {}

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
