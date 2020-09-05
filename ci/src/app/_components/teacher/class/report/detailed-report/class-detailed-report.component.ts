import {Component, Input, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';

@Component({
    selector: 'app-class-detailed-report',
    templateUrl: './class-detailed-report.component.html',
    styleUrls: ['./class-detailed-report.component.scss'],
    providers: []
})
export class ClassDetailedReportComponent implements OnInit {

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

function compare(a: number | string, b: number | string, isAsc: boolean) {
    if (typeof a === 'string' || typeof b === 'string') {
        a = ('' + a).toLowerCase();
        b = ('' + b).toLowerCase();
    }
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
