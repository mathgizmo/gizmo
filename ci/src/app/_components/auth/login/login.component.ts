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
    public ignoreCaptcha = environment.ignoreCaptcha || false;
    private token = null;

    private sub: any;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private authenticationService: AuthenticationService) {
        const navigation = this.router.getCurrentNavigation();
        const state = navigation && navigation.extras && navigation.extras.state;
        if (state && state.error) {
            this.error = state.error;
        }
    }

    ngOnInit() {
        this.sub = this.route.queryParams.subscribe(params => {
            this.token = params['token'] || null;
            if (this.token) {
                this.authenticationService.loginByToken(this.token)
                    .subscribe(user => {
                        if (user && user.user_id) {
                            if (user.role === 'self_study') {
                                this.router.navigate(['/']);
                            } else {
                                if (user.role !== 'teacher' && isNaN(+localStorage.getItem('app_id'))) {
                                    localStorage.setItem('redirect_to', '/');
                                    this.router.navigate(['to-do']);
                                }
                                this.router.navigate(['dashboard']);
                            }
                        }
                    }, error => {
                        this.authenticationService.logout();
                        this.router.navigate(['login']);
                    });
            }
        });
    }

    login() {
        if (this.model.email && this.model.password && (this.captchaResponse || this.ignoreCaptcha)) {
            this.loading = true;
            this.authenticationService.login(this.model.email, this.model.password, this.captchaResponse, this.ignoreCaptcha)
                .subscribe(user => {
                    if (user && user.user_id) {
                        if (user.role === 'self_study') {
                            this.router.navigate(['/']);
                        } else {
                            if (user.role !== 'teacher' && isNaN(+localStorage.getItem('app_id'))) {
                                localStorage.setItem('redirect_to', '/');
                                this.router.navigate(['to-do']);
                            }
                            this.router.navigate(['dashboard']);
                        }
                    } else {
                        this.error = 'Username or password is incorrect!';
                        this.loading = false;
                    }
                }, error => {
                    if (error === 'email_not_verified') {
                        this.router.navigate(['verify-email'], {
                            state: {
                                error: 'Your email address is not verified!',
                                email: this.model.email,
                            }
                        });
                    } else {
                        let message = '';
                        if (typeof error === 'object') {
                            Object.values(error).forEach(x => {
                                message += x + ' ';
                            });
                        } else {
                            message = error;
                        }
                        this.error = message || 'Username or password is incorrect!';
                        this.loading = false;
                    }
                });
        }
    }

    public resolved(captchaResponse: string) {
        this.captchaResponse = captchaResponse;
        this.login();
    }
}
