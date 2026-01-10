import {Component, Input, OnInit} from '@angular/core';
import {DashboardService} from '../../../_services/dashboard.service';

@Component({
    selector: 'app-ad-banner',
    templateUrl: './ad-banner.component.html',
    styleUrls: ['./ad-banner.component.scss'],
    providers: [DashboardService]
})
export class AdBannerComponent implements OnInit {
    @Input() classId: number;
    @Input() assignmentId: number;

    public showAd = false;
    public adCode = '';
    public adMessage = '';

    constructor(private dashboardService: DashboardService) {}

    ngOnInit() {
        this.loadAdSettings();
    }

    loadAdSettings() {
        this.dashboardService.getAdSettings(this.classId, this.assignmentId)
            .subscribe(settings => {
                const hasDonated = settings.has_donated;
                this.adCode = settings.ad_code;
                this.adMessage = settings.ad_message;

                // Show ad only if user hasn't donated and ad code exists
                this.showAd = !hasDonated && !!this.adCode;

                if (this.showAd) {
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
