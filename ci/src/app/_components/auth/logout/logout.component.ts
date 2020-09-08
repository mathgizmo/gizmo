import {Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';
import {AuthenticationService} from '../../../_services';


@Component({
    template: ''
})

export class LogoutComponent implements OnInit {

    constructor(private authenticationService: AuthenticationService, private router: Router) {}

    ngOnInit() {
        this.authenticationService.logout();
        this.router.navigate(['login']);
    }

}
