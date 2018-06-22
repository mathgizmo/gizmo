import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map, catchError, finalize } from 'rxjs/operators';
import { environment } from '../../environments/environment';
import { Router } from '@angular/router';

import { AuthenticationService } from './authentication.service';


@Injectable()
export class HttpService {
    private headers?: HttpHeaders;
    private readonly apiUrl = environment.apiUrl;

    constructor(
        private http: HttpClient,
        private router: Router,
        private authenticationService: AuthenticationService) {
    }

    post(url: string, body: any, auth: boolean = true) {
        if (auth) {
            // add authorization header with jwt token
            this.headers = new HttpHeaders({ 'Authorization': 'Bearer '
                + this.authenticationService.token, 'Content-Type': 'application/json' });
        } else {
            // add authorization header with jwt token
            this.headers = new HttpHeaders({'Content-Type': 'application/json' });
        }

        // post to api
        return this.http.post(this.apiUrl+url, body, { headers: this.headers } )
            .pipe(
                map((response: Response) => response['message']),
                catchError((response: Response) => {
                    if (response['status_code'] == 401) {
                        this.authenticationService.logout();
                        this.router.navigate(['login']);
                    }
                    return response['message'];
                }),
                finalize(() => {
                })
            );
    }

    get(url: string, auth: boolean = true) {
        if (auth) {
            // add authorization header with jwt token
            this.headers = new HttpHeaders({ 'Authorization': 'Bearer '
                + this.authenticationService.token, 'Content-Type': 'application/json' });
        } else {
            // add authorization header with jwt token
            this.headers = new HttpHeaders({'Content-Type': 'application/json' });
        }

        // get from api
        return this.http.get(this.apiUrl+url, { headers: this.headers } )
            .pipe(
                map((response: Response) => response['message']),
                catchError((response: Response) => {
                    if (response['status_code'] == 401) {
                        this.authenticationService.logout();
                        this.router.navigate(['login']);
                    }
                    return response['message'];
                }),
                finalize(() => {
                })
            );
    }
}