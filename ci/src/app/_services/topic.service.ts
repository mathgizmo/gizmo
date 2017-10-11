import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';
import { GlobalVariable } from 'app/globals';

import { AuthenticationService } from './index';

@Injectable()
export class TopicService {
    constructor(
        private http: Http,
        private authenticationService: AuthenticationService) {
    }

    getTopics() {
        // add authorization header with jwt token
        let headers = new Headers({ 'Authorization': 'Bearer ' + this.authenticationService.token, 'Content-Type': 'application/json' });
        let options = new RequestOptions({ headers: headers });

        // get users from api
        return this.http.get(GlobalVariable.BASE_API_URL+'/topic', options)
            .map((response: Response) => response.json().message);
    }
}