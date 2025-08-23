import {Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';

import {AuthenticationService, CountryService} from '../../../_services/index';
import {TagService} from '../../../_services/tag.service';
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
    public error: string;
    public message: string;
    public isRoleSelected = false;
    public isInterestsSelected = false; // new step
    public countries = [];
    public selectedCountry = {
        id: 1,
        title: 'Canada',
        code: 'CA'
    };
    public captchaResponse = '';
    public siteKey = environment.captchaKey || '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    public ignoreCaptcha = environment.ignoreCaptcha || false;
    // tags
    public tags: any[] = [];
    public selectedTagIds: number[] = [];

    constructor(
        private router: Router,
        private authenticationService: AuthenticationService,
        private countryService: CountryService,
        private tagService: TagService) { // new
        const navigation = this.router.getCurrentNavigation();
        const state = navigation && navigation.extras && navigation.extras.state;
        if (state && state.email) {
            this.model.email = state.email;
        }
        if (state && state.role) {
            this.onRoleSelected(state.role);
        }
        if (state && state.error) {
            this.error = state.error;
        }
        if (state && state.message) {
            this.message = state.message;
        }
    }

    ngOnInit() {
        this.authenticationService.logout();
        this.countryService.getCountries().subscribe(countries => {
            this.countries = countries;
            this.selectedCountry = countries.filter(x => x.code === 'CA')[0];
        });
        // load tags early
        this.tagService.getTags().subscribe(tags => { this.tags = tags; });
    }

    // proceed from interests step to personal info
    proceedInterests() {
        this.isInterestsSelected = true;
    }

    toggleTag(tagId: number) {
        const idx = this.selectedTagIds.indexOf(tagId);
        if (idx >= 0) {
            this.selectedTagIds.splice(idx, 1);
        } else {
            this.selectedTagIds.push(tagId);
        }
    }

    register() {
        if (this.model.email && this.model.password && (this.captchaResponse || this.ignoreCaptcha)) {
            this.loading = true;
            const tags = this.selectedTagIds; // gather tags
            this.authenticationService.register(this.model.email,
                this.model.password, this.model.first_name, this.model.last_name,
                this.model.role, this.selectedCountry.id, this.captchaResponse, this.ignoreCaptcha, tags)
                .subscribe(() => {
                    this.router.navigate(['verify-email'], {
                        state: {
                            email: this.model.email,
                            message: 'We sent email verification link to your email address!'
                        }
                    });
                }, error => {
                    if (error.error && error.error['message'] && error.error['message']['email']) {
                        this.error = error.error['message']['email'];
                    } else if (error.error && error.error['message'] && error.error['message']['password']) {
                        this.error = error.error['message']['password'];
                    } else {
                        let message = '';
                        if (typeof error === 'object') {
                            Object.values(error).forEach(x => {
                                message += x + ' ';
                            });
                        } else {
                            message = error;
                        }
                        this.error = message;
                    }
                    this.loading = false;
                });
        }
    }

    onRoleSelected(role) {
        this.model.role = role;
        this.isRoleSelected = true;
        // Skip interests step for students; keep it for teachers/researchers
        if (role === 'student') {
            this.isInterestsSelected = true; // go straight to registration form
        } else {
            this.isInterestsSelected = false; // show interests step first
        }
    }

    public resolved(captchaResponse: string) {
        this.captchaResponse = captchaResponse;
        this.register();
    }
}
