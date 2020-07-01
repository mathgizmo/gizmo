import {Component, OnInit, OnDestroy} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';

import { flatMap } from 'rxjs/operators';
import { Observable, Subscriber } from 'rxjs';

import {AuthenticationService, TopicService, TrackingService} from '../../_services/index';
import {environment} from '../../../environments/environment';
import {NavigationEnd, Router} from '@angular/router';

@Component({
    moduleId: module.id,
    templateUrl: 'home.component.html',
    providers: [TopicService, TrackingService],
    styleUrls: ['home.component.scss']
})

export class HomeComponent implements OnInit, OnDestroy {
    topicsTree: any = [];
    private readonly adminUrl = environment.adminUrl;

    private routerEvent;

    constructor(private router: Router,
                private topicService: TopicService,
                private trackingService: TrackingService,
                private sanitizer: DomSanitizer,
                private authenticationService: AuthenticationService) {
    }

    ngOnInit() {
        this.routerEvent = this.router.events.subscribe((evt) => {
            if (evt instanceof NavigationEnd) {
                this.initData();
            }
        });
        this.initData();
    }

    ngOnDestroy() {
        this.routerEvent.unsubscribe();
    }

    initData() {
        const user = this.authenticationService.userValue;
        const result = this.topicService.getTopics().pipe(
            flatMap(topicsTree => {
                this.topicsTree = topicsTree;
                if (!isNaN(+localStorage.getItem('last-visited-unit-id'))) {
                    return new Observable<object>((subscriber: Subscriber<object>) => subscriber.next({
                        'id': +localStorage.getItem('last-visited-unit-id')
                    }));
                } else if (user && user.user_id > 0) {
                    return this.trackingService.getLastVisitedUnit(user.user_id);
                } else {
                    return new Observable<void>(observer => observer.complete());
                }
            })
        );
        result.subscribe(res => {
            let found = false;
            if (res.id && res.id > 0) {
                for (const item of this.topicsTree) {
                    for (const unit of item.units) {
                        if (!found && unit.id === res.id) {
                            setTimeout(() => {
                                $('#unit' + unit.id + '-topics').slideDown('slow');
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
                                $('#unit' + unit.id + '-topics').slideDown('slow');
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
