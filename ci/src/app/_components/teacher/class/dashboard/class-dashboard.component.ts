import {Component, OnInit} from '@angular/core';
import {ClassesManagementService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';

@Component({
    selector: 'app-class-dashboard',
    templateUrl: './class-dashboard.component.html',
    styleUrls: ['./class-dashboard.component.scss'],
    providers: [ClassesManagementService]
})
export class ClassDashboardComponent implements OnInit {

    classId: number;

    class = {
        name: ''
    };

    public backLinkText = 'Back';

    private sub: any;

    constructor(
        private route: ActivatedRoute,
        private classService: ClassesManagementService) {
    }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            const classes = this.classService.classes;
            this.class = classes.filter(x => x.id === this.classId)[0];
            this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Dashboard';
        });
    }

}
