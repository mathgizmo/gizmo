import {Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';

import {AuthenticationService, CountryService} from '../../../_services/index';
import {environment} from '../../../../environments/environment';

@Component({
    moduleId: module.id,
    templateUrl: 'register.component.html',
    styleUrls: ['register.component.scss']
})

export class RegisterComponent implements OnInit {
    public model: any = {
        country_id: 1
    };
    public loading = false;
    public error = '';
    public isRoleSelected = false;
    public countries = [];
    public selectedCountry = {
        id: 1,
        title: 'Canada',
        code: 'CA'
    };
    public captchaResponse = '';
    public siteKey = environment.captchaKey || '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';

    constructor(
        private router: Router,
        private authenticationService: AuthenticationService,
        private countryService: CountryService) {
    }

    ngOnInit() {
        this.authenticationService.logout();
        this.countryService.getCountries().subscribe(countries => {
            this.countries = countries;
            this.selectedCountry = countries.filter(x => x.code === 'CA')[0];
        });
    }

    register() {
        if (this.model.username && this.model.email && this.model.password && this.captchaResponse) {
            this.loading = true;
            this.authenticationService.register(this.model.username, this.model.email,
                this.model.password, this.model.first_name, this.model.last_name,
                this.model.role, this.selectedCountry.id, this.captchaResponse)
                .subscribe(success => {
                    this.authenticationService.login(this.model.email, this.model.password, this.captchaResponse)
                        .subscribe(user => {
                            if (user.role === 'teacher') {
                                this.router.navigate(['teacher/class']);
                            } else {
                                this.router.navigate(['dashboard']);
                            }
                        });
                }, error => {
                    if (error.error['message']['email']) {
                        this.error = error.error['message']['email'];
                    } else {
                        this.error = error.error['message']['password'];
                    }
                    this.loading = false;
                });
        }
    }

    onRoleSelected(role) {
        this.model.role = role;
        this.isRoleSelected = true;
    }

    public resolved(captchaResponse: string) {
        this.captchaResponse = captchaResponse;
        this.register();
    }
}
