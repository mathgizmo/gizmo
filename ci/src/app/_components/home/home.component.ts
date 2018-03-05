import { Component, OnInit } from '@angular/core';

import { TopicService } from '../../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'home.component.html',
    providers: [TopicService]
})

export class HomeComponent implements OnInit {
    topicsTree: any = [];

    constructor(private topicService: TopicService) { }

    ngOnInit() {
        // get topics tree from API
        this.topicService.getTopics()
            .subscribe(topicsTree => {
                this.topicsTree = topicsTree;
            });
    }

}