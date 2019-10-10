import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';

import { flatMap } from 'rxjs/operators';
import { Observable } from 'rxjs';

import {TopicService, TrackingService} from '../../_services/index';
import {environment} from '../../../environments/environment';

@Component({
    moduleId: module.id,
    templateUrl: 'home.component.html',
    providers: [TopicService, TrackingService],
    styleUrls: ['home.component.scss']
})

export class HomeComponent implements OnInit, OnDestroy {
    topicsTree: any = [];
    private readonly adminUrl = environment.adminUrl;

    constructor(private topicService: TopicService,
                private trackingService: TrackingService,
                private sanitizer: DomSanitizer) {
    }

    ngOnInit() {
        const currentUser = JSON.parse(localStorage.getItem('currentUser'));
        const result = this.topicService.getTopics().pipe(
            flatMap(topicsTree => {
                this.topicsTree = topicsTree;
                if (currentUser && currentUser.user_id > 0) {
                    return this.trackingService.getLastVisitedUnit(currentUser.user_id);
                } else {
                    return new Observable<void>(observer => observer.complete());
                }
            })
        );
        result.subscribe(res => {
            let found = false;
            if (res.id > 0) {
                for (const item of this.topicsTree) {
                    for (const unit of item.units) {
                        if (!found && unit.id === res.id) {
                            setTimeout(() => {
                                $('#unit' + unit.id + '-topics').slideDown("slow");
                                $('html, body').animate({
                                    scrollTop: ($('#unit' + res.id).offset().top) - 8
                                }, 1000);
                            }, 100);
                            found = true;
                            unit.show = true;
                        } else {
                            unit.show = false;
                        }
                    }
                }
            }
            if (!found) {
                for (const item of this.topicsTree) {
                    for (const unit of item.units) {
                        if (!found && unit.status !== 1) {
                            setTimeout(() => {
                                $('#unit' + unit.id + '-topics').slideDown("slow");
                                $('html, body').animate({
                                    scrollTop: ($('#unit' + unit.id).offset().top) - 8
                                }, 1000);
                            }, 100);
                            found = true;
                            unit.show = true;
                            break;
                        }
                    }
                    if (found) {
                        break;
                    }
                }
            }
        });
        /* old unused scroll pt.1/2
        setTimeout(() => {
            if (!isNaN(+localStorage.getItem('home-scroll'))) {
                window.scroll(0, +localStorage.getItem('home-scroll'));
            }
        }, 10); */
    }

    ngOnDestroy() {
        /* old unused scroll pt.2/2
        const doc = document.documentElement;
        const top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
        localStorage.setItem('home-scroll', JSON.stringify(top)); */
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
        $('#unit' + item.id + '-topics').slideToggle('slow');
        item.show = !item.show;
    }

}
