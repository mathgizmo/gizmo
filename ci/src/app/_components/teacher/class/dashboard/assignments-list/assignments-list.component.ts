import {Component, Input, OnInit} from '@angular/core';
import {Sort} from '@angular/material/sort';
import {environment} from '../../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {compare} from '../../../../../_helpers/compare.helper';

@Component({
    selector: 'app-class-assignments-list',
    templateUrl: './assignments-list.component.html',
    styleUrls: ['./assignments-list.component.scss']
})
export class ClassAssignmentsListComponent implements OnInit {

    @Input() classId: number;
    @Input() assignments = [];
    public currentDate = (new Date()).toISOString().split('T')[0];
    private readonly adminUrl = environment.adminUrl;

    constructor(
        private sanitizer: DomSanitizer) {
    }

    ngOnInit() {
    }

    sortData(sort: Sort) {
        const data = this.assignments.slice();
        if (!sort.active || sort.direction === '') {
            this.assignments = data;
            return;
        }
        this.assignments = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'start_date': return compare(a.start_date, b.start_date, isAsc);
                case 'start_time': return compare(a.start_time, b.start_time, isAsc);
                case 'due_date': return compare(a.due_date, b.due_date, isAsc);
                case 'due_time': return compare(a.due_time, b.due_time, isAsc);
                case 'progress': return compare(a.progress, b.progress, isAsc);
                case 'error_rate': return compare(a.error_rate, b.error_rate, isAsc);
                case 'class_error_rate': return compare(a.class_error_rate, b.class_error_rate, isAsc);
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
