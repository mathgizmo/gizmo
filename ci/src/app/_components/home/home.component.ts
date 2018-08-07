import { Component, OnInit } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';

import { TopicService } from '../../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'home.component.html',
    providers: [TopicService],
    styleUrls: ['home.component.scss']
})

export class HomeComponent implements OnInit {
    topicsTree: any = [];
    topicIcon: any;

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
        return this.sanitizer.bypassSecurityTrustStyle(`url(./assets/${image})`);
    }

}