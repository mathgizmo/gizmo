import {Component, OnInit} from '@angular/core';
import {User} from '../../_models/user';
import {AuthenticationService, CountryService, UserService} from '../../_services/index';
import {DomSanitizer} from '@angular/platform-browser';
import {Router} from '@angular/router';
import {environment} from '../../../environments/environment';

@Component({
    selector: 'app-profile',
    templateUrl: './profile.component.html',
    styleUrls: ['./profile.component.scss'],
    providers: [UserService]
})
export class ProfileComponent implements OnInit {
    user: User;
    passwordsMatch = true;
    badEmail = false;
    warningMessage: string;

    public applications = [];
    public selectedAppId;
    public countries = [];
    public selectedCountry = {
        id: 1,
        title: 'Canada',
        code: 'CA'
    };

    private readonly adminUrl = environment.adminUrl;

    constructor(
        private userService: UserService,
        private authenticationService: AuthenticationService,
        private countryService: CountryService,
        private sanitizer: DomSanitizer,
        private router: Router
    ) {
        this.user = new User();
    }

    ngOnInit() {
        this.countryService.getCountries().subscribe(countries => {
            this.countries = countries;
            this.userService.getProfile()
                .subscribe(res => {
                    localStorage.setItem('app_id', res['app_id']);
                    this.selectedAppId = res['app_id'];
                    this.user.username = res['name'];
                    this.user.first_name = res['first_name'];
                    this.user.last_name = res['last_name'];
                    this.user.email = res['email'];
                    this.user.question_num = res['question_num'];
                    this.user.country_id = res['country_id'];
                    localStorage.setItem('question_num', res['question_num']);
                    this.applications = res['applications'];
                    const userCountry = this.countries.filter(x => x.id === this.user.country_id);
                    if (userCountry.length > 0) {
                        this.selectedCountry = userCountry[0];
                    } else {
                        this.selectedCountry = this.countries.filter(x => x.code === 'CA')[0];
                    }
                });
        });
    }

    onChangeProfile() {
        this.user.country_id = this.selectedCountry.id;
        this.userService.changeProfile(this.user)
            .subscribe(res => {
                this.passwordsMatch = true;
                if (Array.isArray(res['email'])) {
                    this.warningMessage = res['email'][0];
                    this.badEmail = true;
                } else {
                    localStorage.setItem('question_num', '' + this.user.question_num);
                    this.badEmail = false;
                }
            });
    }

    onChangePassword(newPassword: string, confirmedPassword: string) {
        this.badEmail = false;
        if (newPassword !== confirmedPassword) {
            this.passwordsMatch = false;
            this.warningMessage = 'Password does not match the confirm password!';
            return;
        } else if (newPassword === '') {
            this.passwordsMatch = false;
            this.warningMessage = 'You can\'t use empty passwords!';
            return;
        } else {
            this.passwordsMatch = true;
            this.userService.changePassword(newPassword, confirmedPassword)
                .subscribe(res => {
                    this.authenticationService.login(this.user.email, newPassword);
                }, error => {
                    console.log(error);
                });
        }
    }

    onChangeApplication(appId: number) {
        if (!appId) {
            return;
        }
        this.userService.changeApplication(appId)
            .subscribe(res => {
                localStorage.setItem('app_id', appId + '');
                this.selectedAppId = appId;
                const redirectTo = localStorage.getItem('redirect_to');
                if (redirectTo) {
                    localStorage.removeItem('redirect_to');
                    this.router.navigate([redirectTo]);
                } else {
                    this.router.navigate(['/']);
                }
            }, error => {
                // console.log(error);
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
