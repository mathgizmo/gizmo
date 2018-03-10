import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { environment } from '../../environments/environment';
import { Router } from '@angular/router';

import { AuthenticationService } from './authentication.service';

@Injectable()
export class HttpService {
    private headers?: Headers;
    private readonly apiUrl = environment.apiUrl;

    constructor(
        private http: Http,
        private router: Router,
        private authenticationService: AuthenticationService) {
    }

    post(url: string, body: string, auth: boolean = true) {
        if (auth) {
            // add authorization header with jwt token
            this.headers = new Headers({ 'Authorization': 'Bearer '
                + this.authenticationService.token, 'Content-Type': 'application/json' });
        } else {
            // add authorization header with jwt token
            this.headers = new Headers({'Content-Type': 'application/json' });
        }
        let options = new RequestOptions({ headers: this.headers });

        // post to api
        return this.http.post(this.apiUrl+url, body, options)
            .map((response: Response) => response.json().message)
            .catch((response: Response) => {
                var json = response.json();
                if (json.status_code == 401) {
                    this.authenticationService.logout();
                    this.router.navigate(['login']);
                }
                return response.json().message;
            });;
    }

    get(url: string, auth: boolean = true) {
        if (auth) {
            // add authorization header with jwt token
            this.headers = new Headers({ 'Authorization': 'Bearer '
                + this.authenticationService.token, 'Content-Type': 'application/json' });
        } else {
            // add authorization header with jwt token
            this.headers = new Headers({'Content-Type': 'application/json' });
        }
        let options = new RequestOptions({ headers: this.headers });

        // get from api
        return this.http.get(this.apiUrl+url, options)
            .map((response: Response) => response.json().message)
            .catch((response: Response) => {
                var json = response.json();
                if (json.status_code == 401) {
                    this.authenticationService.logout();
                    this.router.navigate(['login']);
                }
                return response.json().message;
            });
    }
}