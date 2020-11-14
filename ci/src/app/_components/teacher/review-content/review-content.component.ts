import {Component, OnInit} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';

import {ContentService} from '../../../_services';
import {environment} from '../../../../environments/environment';
import {Router} from '@angular/router';

@Component({
    moduleId: module.id,
    templateUrl: 'review-content.component.html',
    providers: [ContentService],
    styleUrls: ['review-content.component.scss']
})

export class ReviewContentComponent implements OnInit {
    topicsTree: any = [];
    private readonly adminUrl = environment.adminUrl;

    constructor(private router: Router,
                private contentService: ContentService,
                private sanitizer: DomSanitizer) {
    }

    ngOnInit() {
        this.contentService.getContent()
            .subscribe(res => {
                this.topicsTree = res;
                const lastVisitedTopic = +localStorage.getItem('last-visited-topic-id');
                if (lastVisitedTopic) {
                    let found = false;
                    for (const item of this.topicsTree) {
                        for (const unit of item.units) {
                            unit.show = false;
                            for (const topic of unit.topics) {
                                if (!found && topic.id === lastVisitedTopic) {
                                    setTimeout(() => {
                                        $('#unit' + unit.id + '-topics').slideDown('slow');
                                        $('#topic' + lastVisitedTopic + '-lessons').slideDown('slow');
                                        $('html, body').animate({
                                            scrollTop: ($('#topic' + lastVisitedTopic).offset().top) - 8
                                        }, 1000);
                                    }, 100);
                                    topic.show = true;
                                    unit.show = true;
                                    found = true;
                                } else {
                                    topic.show = false;
                                }
                            }
                        }
                    }
                }
            });
    }

    slideToggle(item: any) {
        $('#unit' + item.id + '-topics').slideToggle('slow');
        item.show = !item.show;
    }

    slideToggleTopic(item: any) {
        $('#topic' + item.id + '-lessons').slideToggle('slow');
        item.show = !item.show;
    }

    setTopicIcon(image) {
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}
