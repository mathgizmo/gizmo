import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';

import { AuthenticationService, WelcomeService } from '../../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'welcome.component.html',
    styleUrls: ['welcome.component.scss'],
    providers: [WelcomeService]
})

export class WelcomeComponent implements OnInit {
    model: any = {};
    loading = false;
    error = '';

    title = 'Welcome to Health Numeracy Learning Object!';
    subtitle = 'Improve your numeracy skills. Sharpen your reasoning. Learn something new.';
    introduction = 'Once you log in, you will see all topics, organized into units and levels. Each topic contains a sequence of lessons. To complete a lesson, you need to correctly answer several questions (this number can be changed in your settings). There are different ways to advance through the material. You can take a placement test and skip some topics. Or, once inside a topic, you can test out of it and move to the next topic. You can always go back to any topic or any lesson you have completed, to review it.';

    constructor(
        private router: Router,
        private activatedRoute: ActivatedRoute,
        private authenticationService: AuthenticationService,
        private welcomeService: WelcomeService) {
    }

    ngOnInit() {
        // reset login status
        this.authenticationService.logout();
        // get texts
        this.welcomeService.getWelcomeTexts()
            .subscribe(result => {
                for(let i = 0; i < result.length; i++) {
                    switch (result[i].key) {
                        case 'Home1':
                            this.title = result[i].value;
                            break;
                        case 'Home2':
                            this.subtitle = result[i].value;
                            break;
                        case 'Home3':
                            this.introduction = result[i].value;
                            break;
                        default:
                            break;
                    }
                }
            });
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
