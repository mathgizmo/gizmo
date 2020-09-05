import {Component, OnInit} from '@angular/core';
import {UserService} from '../../../_services/user.service';
import {Sort} from '@angular/material/sort';

@Component({
    selector: 'app-my-invitations',
    templateUrl: './invitations.component.html',
    styleUrls: ['./invitations.component.scss'],
    providers: [UserService]
})
export class MyInvitationsComponent implements OnInit {

    public availableClasses = [];
    public idFilter;
    public nameFilter;
    public teacherFilter;

    constructor(private userService: UserService) {
    }

    ngOnInit() {
        this.userService.getClassInvitations()
            .subscribe(response => {
                this.availableClasses = response;
            });
    }

    onSubscribe(class_id) {
        this.userService.subscribeClass(class_id)
            .subscribe(response => {
                this.availableClasses = this.availableClasses.filter( (item) => {
                    return item.id !== class_id;
                });
            });
    }

    sortAvailableClasses(sort: Sort) {
        const data = this.availableClasses.slice();
        if (!sort.active || sort.direction === '') {
            this.availableClasses = data;
            return;
        }
        this.availableClasses = data.sort((a, b) => {
            const isAsc = sort.direction === 'asc';
            switch (sort.active) {
                case 'id': return compare(a.id, b.id, isAsc);
                case 'name': return compare(a.name, b.name, isAsc);
                case 'teacher': return compare(a.teacher, b.teacher, isAsc);
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
