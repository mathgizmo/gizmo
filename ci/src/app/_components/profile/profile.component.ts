import {Component, OnInit} from '@angular/core';
import {User} from '../../_models/user';
import {UserService} from '../../_services/user.service';
import {AuthenticationService} from '../../_services/authentication.service';

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

    constructor(
        private userService: UserService,
        private authenticationService: AuthenticationService
    ) {
        this.user = new User();
    }

    ngOnInit() {
        this.userService.getProfile()
            .subscribe(res => {
                this.user.username = res['name'];
                this.user.first_name = res['first_name'];
                this.user.last_name = res['last_name'];
                this.user.email = res['email'];
                this.user.questionNum = res['question_num'];
                localStorage.setItem('question_num', res['question_num']);
            });
    }

    onChangeProfile() {
        this.userService.changeProfile(this.user)
            .subscribe(res => {
                this.passwordsMatch = true;
                if (Array.isArray(res['email'])) {
                    this.warningMessage = res['email'][0];
                    this.badEmail = true;
                } else {
                    localStorage.setItem('question_num', '' + this.user.questionNum);
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

}
