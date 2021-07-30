import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import {AuthenticationService, UserService} from '../../_services';
import {User} from '../../_models';
import {MatSnackBar} from '@angular/material/snack-bar';

@Component({
    selector: 'app-class-join',
    templateUrl: './class-join.component.html',
    styleUrls: ['./class-join.component.scss']
})
export class ClassJoinComponent implements OnInit {

    public classKey: string;
    public class = {
        id: 0,
        key: null,
        name: '',
        class_type: 'other',
        subscription_type: 'open',
        invitations: ''
    };

    public user: User;
    public email: string;

    private sub: any;

    constructor(private route: ActivatedRoute,
                private router: Router,
                private authenticationService: AuthenticationService,
                private userService: UserService,
                public snackBar: MatSnackBar) {
    }

    public ngOnInit() {
        this.authenticationService.user.subscribe(x => {
            if (x) {
                this.user = x;
            }
        });
        this.sub = this.route.params.subscribe(params => {
            this.classKey = params['class_key'];
        });
    }

    public subscribeClass() {
        this.userService.subscribeClass(this.classKey)
            .subscribe(response => {
                this.class = response;
                this.router.navigate(['student/class/' + this.class.id + '/report']);
            }, error => {
                let message = '';
                if (typeof error === 'object') {
                    Object.values(error).forEach(x => {
                        message += x + ' ';
                    });
                } else {
                    message = error;
                }
                this.snackBar.open(message ? message : 'Error occurred while subscribing the classroom!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    public checkEmail() {
        this.authenticationService.checkEmail(this.email).subscribe(res => {
            localStorage.setItem('redirect_to', this.router.url + '');
            const redirect = res && res.is_registered ? 'login' : 'register';
            const message = res && res.is_registered ? 'Login to your account to continue!' : 'Register new account to continue!';
            this.router.navigate([redirect], {
                state: {
                    email: this.email,
                    role: 'student',
                    message: message
                }
            });
        }, error => {
            this.router.navigate(['login'], {
                state: {
                    email: this.email
                }
            });
        });
    }

}
