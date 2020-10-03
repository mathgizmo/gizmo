import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {DomSanitizer} from '@angular/platform-browser';
import {TopicService} from '../../../_services';
import {environment} from '../../../../environments/environment';

@Component({
    moduleId: module.id,
    templateUrl: 'topic.component.html',
    styleUrls: ['topic.component.scss'],
    providers: [TopicService]
})

export class TopicComponent implements OnInit {
    backLinkText = 'Back';
    topicTree: any = [];
    id: number;
    appId: number;
    private sub: any;
    topicDone: boolean;

    private readonly adminUrl = environment.adminUrl;

    constructor(
        private topicService: TopicService,
        private route: ActivatedRoute,
        private sanitizer: DomSanitizer
    ) {
    }

    ngOnInit() {
        this.topicDone = false;
        this.sub = this.route.params.subscribe(params => {
            this.id = +params['topic_id']; // (+) converts string 'id' to a number
            this.appId = +params['app_id'] || +localStorage.getItem('app_id') || 0;
            // In a real app: dispatch action to load the details here.
            // get topics tree from API
            this.topicService.getTopic(this.id, this.appId)
                .subscribe(topicTree => {
                    this.topicTree = topicTree;
                    localStorage.setItem('last-visited-unit-id', this.topicTree.unit_id + '');
                    this.backLinkText = this.topicTree.level + ' > ' + this.topicTree.unit;
                    this.topicDone = (this.topicTree.status === 1);
                });
        });
    }

    setTopicIcon(image) {
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}
