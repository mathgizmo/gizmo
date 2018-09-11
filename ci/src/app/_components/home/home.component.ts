import { Component, OnInit } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ActivatedRoute } from '@angular/router';

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
    private readonly adminUrl = environment.adminUrl;

    constructor(private topicService: TopicService, 
        private sanitizer: DomSanitizer, private activatedRoute: ActivatedRoute) { 
    }

    ngOnInit() {
        // get topics tree from API
        this.topicService.getTopics()
            .subscribe(topicsTree => {
                this.topicsTree = topicsTree;
                this.activatedRoute.params.subscribe(params => {
                    setTimeout(() => {
                        //console.log(window.history);
                        if(document.getElementById('topic'+params['id'])) 
                            document.getElementById('topic'+params['id'])
                                .scrollIntoView();
                    }, 60);
                });
            });
    }

    setTopicIcon(image) {
        let link = `url(`+this.adminUrl+`/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

    /* Gold Icon
    setTopicIconComplete(image) {
        let link = `url(`+this.adminUrl+`/${image}`.slice(0, -4)+`-gold.svg)`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }
    */

}