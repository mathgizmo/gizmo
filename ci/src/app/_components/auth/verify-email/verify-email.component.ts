import {Component, OnInit} from '@angular/core';

import {AuthenticationService} from '../../../_services/index';
import {timer} from 'rxjs';
import {takeWhile, tap} from 'rxjs/operators';
import {ActivatedRoute, Router} from '@angular/router';

@Component({
    moduleId: module.id,
    templateUrl: 'verify-email.component.html',
    styleUrls: ['verify-email.component.scss']
})

export class VerifyEmailComponent implements OnInit {
    public model = {
        email: '',
    };
    public loading = false;
    public error = '';
    public message = '';
    public linkTimer = 0;

    private sub: any;

    constructor(private authenticationService: AuthenticationService, private router: Router, private route: ActivatedRoute) {
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
            const callbackUrl = params['callbackURL'] || null;
            if (callbackUrl) {
                this.loading = true;
                this.error = null;
                this.message = null;
                this.authenticationService.verifyEmail(callbackUrl)
                    .subscribe(res => {
                        if (typeof res === 'object') {
                            this.message = res.message || null;
                            if (res.redirectUrl) {
                                window.location.href = res.redirectUrl;
                            }
                        } else {
                            this.message = res;
                        }
                        this.loading = false;
                        this.initTimer();
                    }, error => {
                        this.error = error;
                        this.loading = false;
                        this.initTimer();
                    });
            }
        });
    }

    initTimer() {
        timer(1000, 1000)
            .pipe(
                takeWhile( () => this.linkTimer > 0 ),
                tap(() => this.linkTimer--)
            )
            .subscribe( () => {});
    }

    verifyEmail() {
        if (this.model.email) {
            this.loading = true;
            this.error = null;
            this.message = null;
            this.authenticationService.sendEmailVerificationLink(this.model.email)
                .subscribe(res => {
                    this.message = res;
                    this.loading = false;
                    this.linkTimer = 60;
                    this.initTimer();
                }, error => {
                    this.error = error;
                    this.loading = false;
                    this.linkTimer = 60;
                    this.initTimer();
                });
        }
    }
}
