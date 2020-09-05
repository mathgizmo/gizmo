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
