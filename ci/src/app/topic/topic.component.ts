﻿import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { TopicService } from '../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'topic.component.html',
    providers: [TopicService]
})

export class TopicComponent implements OnInit {
    topicTree: any = [];
    id: number;
    private sub: any;

    constructor(
            private topicService: TopicService,
            private route: ActivatedRoute
            ) { }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.id = +params['id']; // (+) converts string 'id' to a number

            // In a real app: dispatch action to load the details here.
            // get topics tree from API
            this.topicService.getTopic(this.id)
                .subscribe(topicTree => {
                    this.topicTree = topicTree;
                    console.log(topicTree);
                });
         });

    }

}