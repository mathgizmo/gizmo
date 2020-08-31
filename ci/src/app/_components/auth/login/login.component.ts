import {Component, OnInit, ViewChild} from '@angular/core';
import {Router} from '@angular/router';
import {environment} from '../../../../environments/environment';

import {AuthenticationService} from '../../../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'login.component.html',
    styleUrls: ['login.component.scss']
})

export class LoginComponent implements OnInit {
    public model: any = {};
    public loading = false;
    public error = '';
    public captchaResponse = '';
    public siteKey = environment.captchaKey || '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';

    constructor(
        private router: Router,
        private authenticationService: AuthenticationService) {
    }

    ngOnInit() {
        this.authenticationService.logout();
    }

    login() {
        if (this.model.email && this.model.password && this.captchaResponse) {
            this.loading = true;
            this.authenticationService.login(this.model.email, this.model.password, this.captchaResponse)
                .subscribe(user => {
                    if (user && user.user_id) {
                        if (user.role === 'teacher') {
                            this.router.navigate(['teacher/dashboard']);
                        } else {
                            if (isNaN(+localStorage.getItem('app_id'))) {
                                localStorage.setItem('redirect_to', '/');
                                this.router.navigate(['to-do']);
                            } else {
                                this.router.navigate(['/']);
                            }
                        }
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
