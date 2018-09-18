import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { DomSanitizer } from '@angular/platform-browser';
import { TopicService } from '../../../_services/index';

import { environment } from '../../../../environments/environment';

@Component({
    moduleId: module.id,
    templateUrl: 'topic.component.html',
    styleUrls: ['topic.component.scss'],
    providers: [TopicService]
})

export class TopicComponent implements OnInit {
    backLinkText: string = '<-Back';
    topicTree: any = [];
    id: number;
    private sub: any;
    topicDone: boolean;

    private readonly adminUrl = environment.adminUrl;

    constructor(
        private topicService: TopicService,
        private route: ActivatedRoute,
        private sanitizer: DomSanitizer
    ) { }

    ngOnInit() {
        this. topicDone = false;
        this.sub = this.route.params.subscribe(params => {
            this.id = +params['id']; // (+) converts string 'id' to a number
            // In a real app: dispatch action to load the details here.
            // get topics tree from API
            this.topicService.getTopic(this.id)
                .subscribe(topicTree => {
                    this.topicTree = topicTree;
                    this.backLinkText = this.topicTree.level + ' > ' + this.topicTree.unit;
                    this.topicDone = (this.topicTree.status === 1);
                });
         });
    }

    setTopicIcon(image) {
        let link = `url(`+this.adminUrl+`/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}