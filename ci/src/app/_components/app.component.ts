﻿import {Component, OnInit} from '@angular/core';
import {Router, NavigationEnd, ActivatedRoute} from '@angular/router';
import {map, filter} from 'rxjs/operators';
import {HTTPStatus, AuthenticationService} from '../_services/index';
import {User} from '../_models/user';
import {Observable} from 'rxjs';

declare var $: any;

@Component({
    moduleId: module.id,
    selector: 'app-root',
    templateUrl: 'app.component.html',
    styleUrls: ['app.component.scss'],
    animations: []
})

export class AppComponent implements OnInit {
    public HTTPActivity$: Observable<boolean>;
    public showMenu = this.isLoggedIn();
    public user: User;

    protected isLoggedIn() {
        return !!this.user;
    }

    constructor(private router: Router,
                private activatedRoute: ActivatedRoute,
                private httpStatus: HTTPStatus,
                private authenticationService: AuthenticationService) {
    }

    ngOnInit() {
        this.HTTPActivity$ = this.httpStatus.getHttpStatus();
        this.router.events
            .pipe(
                filter((event) => event instanceof NavigationEnd),
                map(() => this.activatedRoute)
            )
            .subscribe((event) => {
                if (this.router.url === '/login') {
                    this.showMenu = false;
                } else {
                    this.showMenu = this.isLoggedIn();
                }
            });
        this.authenticationService.user.subscribe(x => this.user = x);
    }

    onAssignmentClicked() {
        localStorage.removeItem('app_id');
    }
}
