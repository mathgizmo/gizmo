import {Component} from '@angular/core';
import {Router, NavigationEnd, ActivatedRoute} from '@angular/router';
import {map, filter} from 'rxjs/operators';
import {HTTPStatus, AuthenticationService} from '../_services/index';
import {User} from '../_models/user';

@Component({
    moduleId: module.id,
    selector: 'app-root',
    templateUrl: 'app.component.html',
    styleUrls: ['app.component.scss']
})

export class AppComponent {
    HTTPActivity: boolean;

    public showMenu = this.isLoggedIn();
    public user: User;

    protected isLoggedIn() {
        return !!this.user;
    }

    constructor(private router: Router, private activatedRoute: ActivatedRoute,
                private httpStatus: HTTPStatus, private authenticationService: AuthenticationService) {
        this.httpStatus.getHttpStatus()
            .subscribe((status: boolean) => {
                this.HTTPActivity = status;
            });
        router.events
            .pipe(
                filter((event) => event instanceof NavigationEnd),
                map(() => activatedRoute)
            )
            .subscribe((event) => {
                if (router.url === '/login') {
                    this.showMenu = false;
                } else {
                    this.showMenu = this.isLoggedIn();
                }
            });
        this.authenticationService.user.subscribe(x => this.user = x);
    }
}
