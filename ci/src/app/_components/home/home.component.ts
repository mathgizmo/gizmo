import { Component, OnInit } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';

import { TopicService } from '../../_services/index';
import { environment } from '../../../environments/environment';

@Component({
    moduleId: module.id,
    templateUrl: 'home.component.html',
    providers: [TopicService],
    styleUrls: ['home.component.scss']
})

export class HomeComponent implements OnInit {
    topicsTree: any = [];
    topicIcon: any;
    private readonly adminUrl = environment.adminUrl;

    constructor(private topicService: TopicService, private sanitizer: DomSanitizer) { 
    }

    ngOnInit() {
        // get topics tree from API
        this.topicService.getTopics()
            .subscribe(topicsTree => {
                this.topicsTree = topicsTree;
            });
    }

    setTopicIcon(image) {
        let link = `url(`+this.adminUrl+`/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

    setTopicIconComplete(image) {
        let link = `url(`+this.adminUrl+`/${image}`.slice(0, -4)+`-gold.svg)`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}