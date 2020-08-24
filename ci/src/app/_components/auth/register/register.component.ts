import {Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';

import {AuthenticationService, CountryService} from '../../../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'register.component.html',
    styleUrls: ['register.component.scss']
})

export class RegisterComponent implements OnInit {
    model: any = {
        country_id: 1
    };
    loading = false;
    error = '';
    isRoleSelected = false;
    countries = [];
    selectedCountry = {
        id: 1,
        title: 'Canada',
        code: 'CA'
    };

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
        this.loading = true;
        this.authenticationService.register(this.model.username, this.model.email,
            this.model.password, this.model.first_name, this.model.last_name, this.model.role, this.selectedCountry.id)
            .subscribe(success => {
                this.authenticationService.login(this.model.email, this.model.password)
                    .subscribe(user => {
                        if (user.role === 'teacher') {
                            this.router.navigate(['teacher/class']);
                        } else {
                            this.router.navigate(['/']);
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

    onRoleSelected(role) {
        this.model.role = role;
        this.isRoleSelected = true;
    }
}
