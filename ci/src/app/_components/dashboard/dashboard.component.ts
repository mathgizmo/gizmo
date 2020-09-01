import {Component, OnInit} from '@angular/core';
import {DashboardService} from '../../_services/dashboard.service';

@Component({
    selector: 'app-dashboard',
    templateUrl: './dashboard.component.html',
    styleUrls: ['./dashboard.component.scss'],
    providers: [DashboardService]
})
export class DashboardComponent implements OnInit {

    public dashboards = [];

    constructor(private dashboardService: DashboardService) {}

    ngOnInit() {
        this.dashboardService.getDashboards()
            .subscribe(res => {
                this.dashboards = res;
            });
    }

}
