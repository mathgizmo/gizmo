import { Component } from '@angular/core';
import { Router, NavigationEnd, ActivatedRoute } from '@angular/router';
import { map, filter } from 'rxjs/operators';
import { HTTPStatus } from '../_services/index';

@Component({
    moduleId: module.id,
    selector: 'app-root',
    templateUrl: 'app.component.html',
    styleUrls: ['app.component.scss']
})

export class AppComponent {
    HTTPActivity: boolean;

    public showMenu = this.isLoggedIn();

    protected isLoggedIn() {
        return localStorage.getItem('currentUser')?true:false;
    }

    constructor(
        private router: Router,
        private activatedRoute: ActivatedRoute,
        private httpStatus: HTTPStatus) {
            this.httpStatus.getHttpStatus()
                .subscribe((status: boolean) => {this.HTTPActivity = status;});
            router.events
                .pipe(
                    filter((event) => event instanceof NavigationEnd),
                    map(() => activatedRoute)
                )
                .subscribe((event) => {
                    if (router.url === '/login') {
                        this.showMenu = false;
                    }
                    else {
                        this.showMenu = this.isLoggedIn();
                    }
                });
    };
}