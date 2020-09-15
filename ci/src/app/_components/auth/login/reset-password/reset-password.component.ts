import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {AuthenticationService} from '../../../../_services/index';

@Component({
    selector: 'app-reset-password',
    templateUrl: './reset-password.component.html',
    styleUrls: ['./reset-password.component.scss']
})
export class ResetPasswordComponent implements OnInit {
    showError: boolean;
    warning: string;
    token: string;
    waiting = false;
    newPassword = '';
    confirmedPassword = '';

    constructor(private authenticationService: AuthenticationService, private router: Router, private route: ActivatedRoute) {
        this.showError = true;
        this.route.params.subscribe(params => {
            this.token = params['token'];
        });
    }

    ngOnInit() {
    }

    onChangePassword() {
        if (this.newPassword !== this.confirmedPassword) {
            this.showError = false;
            this.warning = 'Password does not match the confirm password!';
            return;
        } else if (this.newPassword === '') {
            this.showError = false;
            this.warning = 'You can\'t use empty passwords!';
            return;
        } else {
            this.showError = true;
            this.waiting = true;
            this.authenticationService.resetPassword(this.newPassword, this.confirmedPassword, this.token)
                .subscribe(result => {
                    if (result['success']) {
                        this.router.navigate(['/login']);
                    } else {
                        let error = '';
                        const messageArr = result['message']['password'];
                        if (messageArr) {
                            for (let i = 0; i < messageArr.length; i++) {
                                error += messageArr[i] + ' ';
                            }
                        } else {
                            error = result['message'];
                        }
                        this.warning = error;
                        this.showError = false;
                    }
                    this.waiting = false;
                }, error => {
                    this.waiting = false;
                    let message = '';
                    if (typeof error === 'object') {
                        Object.values(error).forEach(x => {
                            message += x + ' ';
                        });
                    } else {
                        message = error;
                    }
                    this.warning = message;
                    this.showError = false;
                });
        }
    }

}
