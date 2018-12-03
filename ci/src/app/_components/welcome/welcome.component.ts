import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';

import { AuthenticationService } from '../../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'welcome.component.html',
    styleUrls: ['welcome.component.scss']
})

export class WelcomeComponent implements OnInit {
    model: any = {};
    loading = false;
    error = '';

    constructor(
        private router: Router,
        private activatedRoute: ActivatedRoute,
        private authenticationService: AuthenticationService) {
    }

    ngOnInit() {
        // reset login status
        this.authenticationService.logout();
    }

    login() {
        this.loading = true;
        this.authenticationService.login(this.model.username, this.model.password)
            .subscribe(result => {
                if (result === true) {
                    this.router.navigate(['/']);
                } else {
                    this.error = 'Username or password is incorrect';
                    this.loading = false;
                }
            });
    }

    scrollToInstruction() {
        this.activatedRoute.params.subscribe(params => {
            const instruction = document.getElementById('instruction');
            setTimeout(() => {
                instruction.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 60);
            setTimeout(() => {
                instruction.focus();
            }, 500);
        });
    }
}
