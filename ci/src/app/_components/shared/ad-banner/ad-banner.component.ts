import {Component, Input, OnInit, ChangeDetectorRef} from '@angular/core';
import {DomSanitizer, SafeHtml} from '@angular/platform-browser';
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
    public adCode: SafeHtml = '';
    public adMessage: SafeHtml = '';

    constructor(
        private dashboardService: DashboardService,
        private sanitizer: DomSanitizer,
        private cdRef: ChangeDetectorRef
    ) {}

    ngOnInit() {
        this.loadAdSettings();
    }

    loadAdSettings() {
        this.dashboardService.getAdSettings(this.classId, this.assignmentId)
            .subscribe(settings => {
                const hasDonated = settings.has_donated;
                this.adCode = this.sanitizer.bypassSecurityTrustHtml(settings.ad_code);
                this.adMessage = this.sanitizer.bypassSecurityTrustHtml(settings.ad_message);

                // Show ad only if user hasn't donated and ad code exists
                this.showAd = !hasDonated && !!settings.ad_code;

                if (this.showAd) {
                    this.cdRef.detectChanges(); // Force DOM update
                    this.injectAdSenseScript();
                    setTimeout(() => this.loadAdSense(), 500); // Increased timeout
                }
            });
    }

    injectAdSenseScript() {
        // Prevent double injection
        const scriptId = 'adsense-script-loader';
        if (document.getElementById(scriptId)) {
            return;
        }

        const script = document.createElement('script');
        script.id = scriptId;
        script.async = true;
        // Using the Publisher ID provided
        script.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5903342696681021';
        script.crossOrigin = 'anonymous';
        document.head.appendChild(script);
    }

    loadAdSense(retryCount = 0) {
        window['adsbygoogle'] = window['adsbygoogle'] || [];
        try {
            window['adsbygoogle'].push({});
        } catch (e) {
            console.error('AdSense push error:', e);
            if (retryCount < 3) {
                 // Retry if width was 0 or other transient error
                 console.log(`Retrying AdSense push (Attempt ${retryCount + 1})...`);
                 setTimeout(() => this.loadAdSense(retryCount + 1), 500);
            }
        }
    }
}
