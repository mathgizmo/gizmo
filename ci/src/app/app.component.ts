import { Component } from '@angular/core';
import { Router, NavigationEnd, ActivatedRoute } from '@angular/router';
import 'rxjs/add/operator/filter';

@Component({
    moduleId: module.id,
    selector: 'app-root',
    templateUrl: 'app.component.html'
})

export class AppComponent {
    public showMenu = this.isLoggedIn();

    protected isLoggedIn() {
        return localStorage.getItem('currentUser')?true:false;
    }

    constructor(
        private router: Router,
        private activatedRoute: ActivatedRoute) {
            router.events
                .filter((event) => event instanceof NavigationEnd)
                .map(() => activatedRoute)
            .subscribe((event) => {
                if (router.url == "/login") {
                    this.showMenu = false;
                }
                else {
                    this.showMenu = this.isLoggedIn();
            }
            });
    };
}