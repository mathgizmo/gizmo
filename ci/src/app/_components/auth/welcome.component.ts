﻿import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';

import {SettingsService} from '../../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'welcome.component.html',
    styleUrls: ['welcome.component.scss'],
    providers: [SettingsService]
})

export class WelcomeComponent implements OnInit {
    title: string;
    subtitle: string;
    introduction: string;
    introductionTitle: string;

    constructor(
        private router: Router,
        private activatedRoute: ActivatedRoute,
        private settingsService: SettingsService) {
    }

    ngOnInit() {
        this.settingsService.getWelcomeTexts()
            .subscribe(result => {
                for (let i = 0; i < result.length; i++) {
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
                        case 'Home4':
                            this.introductionTitle = result[i].value;
                            break;
                        default:
                            break;
                    }
                }
                document.getElementById('subtitle').innerHTML = this.subtitle;
                document.getElementById('introduction').innerHTML = this.introduction;
            });
    }

    scrollToInstruction() {
        this.activatedRoute.params.subscribe(params => {
            const instruction = document.getElementById('instruction');
            setTimeout(() => {
                instruction.scrollIntoView({behavior: 'smooth', block: 'center'});
            }, 60);
            setTimeout(() => {
                instruction.focus();
            }, 500);
        });
    }
}
