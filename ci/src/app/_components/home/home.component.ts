import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';

import {TopicService} from '../../_services/index';
import {environment} from '../../../environments/environment';

@Component({
    moduleId: module.id,
    templateUrl: 'home.component.html',
    providers: [TopicService],
    styleUrls: ['home.component.scss']
})

export class HomeComponent implements OnInit, OnDestroy {
    topicsTree: any = [];
    private readonly adminUrl = environment.adminUrl;

    constructor(private topicService: TopicService,
                private sanitizer: DomSanitizer) {
    }

    ngOnInit() {
        this.topicService.getTopics().subscribe(topicsTree => {
            this.topicsTree = topicsTree;
            let found = false;
            for(let item of this.topicsTree) {
            	for(let unit of item.units) {
					if (!found && unit.status != 1) {
						setTimeout( () => {
							$('#unit'+unit.id+'-topics').slideDown("slow");
						}, 100);
						found = true;
						unit.show = true;
            		} else {
            			unit.show = false;
            		}
            	}
            }
            setTimeout(() => {
                if (!isNaN(+localStorage.getItem('home-scroll'))) {
                    window.scroll(0, +localStorage.getItem('home-scroll'));
                }
            }, 10);
        });
    }

    ngOnDestroy() {
        const doc = document.documentElement;
        const top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
        localStorage.setItem('home-scroll', JSON.stringify(top));
    }

    setTopicIcon(image) {
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

    /* Gold Icon
    setTopicIconComplete(image) {
        let link = `url(`+this.adminUrl+`/${image}`.slice(0, -4)+`-gold.svg)`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }
    */

    slideToggle(item: any) {
    	$('#unit'+item.id+'-topics').slideToggle("slow");
    	item.show = !item.show;
    }

}
