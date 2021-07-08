import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import {AuthenticationService, UserService} from '../../_services';
import {User} from '../../_models';
import {MatSnackBar} from '@angular/material/snack-bar';

@Component({
    selector: 'app-class-join',
    template: '',
    providers: [AuthenticationService, UserService]
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

    private sub: any;

    constructor(private route: ActivatedRoute,
                private router: Router,
                private authenticationService: AuthenticationService,
                private userService: UserService,
                public snackBar: MatSnackBar) {
    }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classKey = params['class_key'];
            this.authenticationService.user.subscribe(x => {
                if (!x) {
                    localStorage.setItem('redirect_to', this.router.url + '');
                    this.router.navigate(['login']);
                    return;
                } else {
                    this.user = x;
                    this.joinClass();
                }
            });
        });
    }

    joinClass() {
        console.log(this.classKey);
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

}
