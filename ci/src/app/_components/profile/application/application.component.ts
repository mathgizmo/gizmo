import {Component, OnInit} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {Router} from '@angular/router';
import {UserService} from '../../../_services/user.service';
import {environment} from '../../../../environments/environment';

@Component({
    selector: 'app-profile-application',
    templateUrl: './application.component.html',
    styleUrls: ['./application.component.scss'],
    providers: [UserService]
})
export class ProfileApplicationComponent implements OnInit {
    public applications = [];
    public selectedAppId = +localStorage.getItem('app_id');
    private readonly adminUrl = environment.adminUrl;

    constructor(
        private userService: UserService,
        private sanitizer: DomSanitizer,
        private router: Router
    ) { }

    ngOnInit() {
        this.userService.getApplications()
            .subscribe(response => {
                this.applications = response;
            });
    }

    onChangeApplication(appId: number) {
        if (!appId) {
            return;
        }
        this.userService.changeApplication(appId)
            .subscribe(res => {
                localStorage.setItem('app_id', appId + '');
                this.selectedAppId = appId;
                console.log(appId);
                const redirectTo = localStorage.getItem('redirect_to');
                if (redirectTo) {
                    localStorage.removeItem('redirect_to');
                    this.router.navigate([redirectTo]);
                }
            }, error => {
                // console.log(error);
            });
    }

    setIcon(image) {
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}
