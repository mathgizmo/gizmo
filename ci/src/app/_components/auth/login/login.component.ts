import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import {environment} from '../../../../environments/environment';

import {AuthenticationService, UserService} from '../../../_services/index';

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
    public error: string;
    public message: string;
    public captchaResponse = '';
    public siteKey = environment.captchaKey || '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    public ignoreCaptcha = environment.ignoreCaptcha || false;
    private token = null;

    private sub: any;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private authenticationService: AuthenticationService,
        private userService: UserService
    ) {
        const navigation = this.router.getCurrentNavigation();
        const state = navigation && navigation.extras && navigation.extras.state;
        if (state && state.email) {
            this.model.email = state.email;
        }
        if (state && state.error) {
            this.error = state.error;
        }
        if (state && state.message) {
            this.message = state.message;
        }
    }

    ngOnInit() {
        this.sub = this.route.queryParams.subscribe(params => {
            this.token = params['token'] || null;
            if (this.token) {
                this.authenticationService.loginByToken(this.token)
                    .subscribe(user => {
                        if (user && user.user_id) {
                            this.afterLoginRedirect(user);
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
                        this.afterLoginRedirect(user);
                    } else {
                        this.error = 'Email or password is incorrect!';
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
                        this.error = message || 'Email or password is incorrect!';
                        this.loading = false;
                    }
                });
        }
    }

    public resolved(captchaResponse: string) {
        this.captchaResponse = captchaResponse;
        this.login();
    }

    private afterLoginRedirect(user) {
        const redirectTo = localStorage.getItem('redirect_to');
        if (redirectTo || user.redirect_to) {
            if (redirectTo) {
                localStorage.removeItem('redirect_to');
                this.router.navigate([redirectTo]);
            } else {
                this.router.navigate([user.redirect_to]);
            }
            this.userService.clearRedirectTo().subscribe();
        } else {
            /* const isFirstTimeLogin = false;
            if ((user.role === 'teacher' || user.role === 'researcher') && isFirstTimeLogin) {
                this.router.navigate(['teacher/class']);
            } */
            user.role === 'self_study'
                ? this.router.navigate(['/'])
                : this.router.navigate(['dashboard']);
        }
    }
}
