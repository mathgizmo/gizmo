import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';
import { GlobalVariable } from 'app/globals';

import { AuthenticationService } from './authentication.service';

@Injectable()
export class ServerService {
    private headers?: Headers;

    constructor(
        private http: Http,
        private authenticationService: AuthenticationService) {
    }

    post(url: string, body: string, auth: boolean = true) {
        if (auth) {
            // add authorization header with jwt token
            this.headers = new Headers({ 'Authorization': 'Bearer ' + this.authenticationService.token, 'Content-Type': 'application/json' });
        } else {
            // add authorization header with jwt token
            this.headers = new Headers({'Content-Type': 'application/json' });
        }
        let options = new RequestOptions({ headers: this.headers });

        // post to api
        return this.http.post(GlobalVariable.BASE_API_URL+url, body, options)
            .map((response: Response) => response.json().message);
    }

    get(url: string, auth: boolean = true) {
        if (auth) {
            // add authorization header with jwt token
            this.headers = new Headers({ 'Authorization': 'Bearer ' + this.authenticationService.token, 'Content-Type': 'application/json' });
        } else {
            // add authorization header with jwt token
            this.headers = new Headers({'Content-Type': 'application/json' });
        }
        let options = new RequestOptions({ headers: this.headers });

        // get from api
        return this.http.get(GlobalVariable.BASE_API_URL+url, options)
            .map((response: Response) => response.json().message);
    }
}