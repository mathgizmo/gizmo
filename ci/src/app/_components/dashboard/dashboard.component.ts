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
    
    public hasDonated = false;

    constructor(private dashboardService: DashboardService) {}

    ngOnInit() {
        this.dashboardService.getDashboards()
            .subscribe(res => {
                this.dashboards = res;

                this.checkDonationStatus();
            });
    }

    checkDonationStatus() {
        this.dashboardService.getDonationStatus()
            .subscribe(status => {
                this.hasDonated = status.has_donated;

                if (!this.hasDonated) {
                    setTimeout(() => this.loadAdSense(), 100);
                }
            });
    }

    loadAdSense() {
        if (window['adsbygoogle']) {
            window['adsbygoogle'] = window['adsbygoogle'] || [];
            window['adsbygoogle'].push({});
        }
    }

}
