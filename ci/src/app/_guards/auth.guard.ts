import {Injectable} from '@angular/core';
import {Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot} from '@angular/router';
import { AuthenticationService } from '../_services';

@Injectable()
export class AuthGuard implements CanActivate {

    constructor(private router: Router, private authenticationService: AuthenticationService) {
    }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
        const user = this.authenticationService.userValue;
        if (user) {
            if (route.data.roles && route.data.roles.indexOf(user.role) === -1) {
                this.router.navigate(['/']);
                return false;
            }
            return true;
        }
        this.router.navigate(['/welcome']);
        return false;
    }
}
