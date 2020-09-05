import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import {environment} from '../../../../environments/environment';

import {AuthenticationService} from '../../../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'login.component.html',
    styleUrls: ['login.component.scss']
})

export class LoginComponent implements OnInit {
    public model = {
        email: '',
        password: ''
    };
    public loading = false;
    public error = '';
    public captchaResponse = '';
    public siteKey = environment.captchaKey || '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    private token = null;

    private sub: any;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private authenticationService: AuthenticationService) {
        this.sub = this.route.queryParams.subscribe(params => {
            this.token = params['token'] || null;
            this.authenticationService.login(this.model.email, this.model.password, this.captchaResponse, this.token)
                .subscribe(user => {
                    if (user && user.user_id) {
                        if (user.role !== 'teacher' && isNaN(+localStorage.getItem('app_id'))) {
                            localStorage.setItem('redirect_to', '/');
                            this.router.navigate(['to-do']);
                        }
                        this.router.navigate(['dashboard']);
                    }
                });
        });
    }

    ngOnInit() {
        this.authenticationService.logout();
    }

    login() {
        if (this.model.email && this.model.password && this.captchaResponse) {
            this.loading = true;
            this.authenticationService.login(this.model.email, this.model.password, this.captchaResponse, this.token)
                .subscribe(user => {
                    if (user && user.user_id) {
                        if (user.role !== 'teacher' && isNaN(+localStorage.getItem('app_id'))) {
                            localStorage.setItem('redirect_to', '/');
                            this.router.navigate(['to-do']);
                        }
                        this.router.navigate(['dashboard']);
                    } else {
                        this.error = 'Username or password is incorrect';
                        this.loading = false;
                    }
                }, error => {
                    this.error = 'Username or password is incorrect';
                    this.loading = false;
                });
        }
    }

    public resolved(captchaResponse: string) {
        this.captchaResponse = captchaResponse;
        this.login();
    }
}
