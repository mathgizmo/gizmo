import {Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';

import {AuthenticationService} from '../../../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'register.component.html',
    styleUrls: ['register.component.scss']
})

export class RegisterComponent implements OnInit {
    model: any = {};
    loading = false;
    error = '';

    constructor(
        private router: Router,
        private authenticationService: AuthenticationService) {
    }

    ngOnInit() {
        this.authenticationService.logout();
    }

    register() {
        this.loading = true;
        this.authenticationService.register(this.model.username, this.model.email,
            this.model.password, this.model.first_name, this.model.last_name)
            .subscribe(result => {
                if (result['success'] === true) {
                    this.authenticationService.login(this.model.email, this.model.password).subscribe(res => {
                        if (res === true) {
                            this.router.navigate(['placement']);
                        }
                    });
                } else {
                    if (result['message']['email']) {
                        this.error = result['message']['email'];
                    } else {
                        this.error = result['message']['password'];
                    }
                    this.loading = false;
                }
            });
    }
}
