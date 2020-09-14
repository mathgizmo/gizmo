import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {map, catchError} from 'rxjs/operators';
import {environment} from '../../environments/environment';
import {NavigationExtras, Router} from '@angular/router';

import {AuthenticationService} from './authentication.service';
import {throwError} from 'rxjs';


@Injectable()
export class HttpService {
    private headers?: HttpHeaders;
    private readonly apiUrl = environment.apiUrl;

    constructor(
        private http: HttpClient,
        private router: Router,
        private authenticationService: AuthenticationService) {
    }

    get(url: string, auth: boolean = true, params = null) {
        if (auth) {
            this.headers = new HttpHeaders({
                'Authorization': 'Bearer '
                    + this.authenticationService.token, 'Content-Type': 'application/json'
            });
        } else {
            this.headers = new HttpHeaders({'Content-Type': 'application/json'});
        }
        return this.http.get(this.apiUrl + url, {headers: this.headers, params: params})
            .pipe(
                map((response: Response) => response['message']),
                catchError((response: Response) => {
                    if (response['status_code'] === 401 || response['error']['status_code'] === 401 ||
                        response['status_code'] === 403 || response['error']['status_code'] === 403) {
                        this.authenticationService.logout();
                        this.router.navigate(['login'], {
                            state: {
                                error: response['error'] && response['error']['message']
                            }
                        });
                    }
                    if (response['status_code'] === 453 || response['error']['status_code'] === 453) {
                        localStorage.setItem('redirect_to', this.router.url + '');
                        this.router.navigate(['to-do']);
                    }
                    return throwError(response['error'] && response['error']['message'] || 'Unknown error!');
                })
            );
    }

    post(url: string, body: any = null, auth: boolean = true, params = null) {
        if (auth) {
            this.headers = new HttpHeaders({
                'Authorization': 'Bearer '
                    + this.authenticationService.token, 'Content-Type': 'application/json'
            });
        } else {
            this.headers = new HttpHeaders({'Content-Type': 'application/json'});
        }
        return this.http.post(this.apiUrl + url, body, {headers: this.headers, params: params})
            .pipe(
                map((response: Response) => response['message']),
                catchError((response: Response) => {
                    if (response['status_code'] === 401 || response['error']['status_code'] === 401 ||
                        response['status_code'] === 403 || response['error']['status_code'] === 403) {
                        this.authenticationService.logout();
                        this.router.navigate(['login'], {
                            state: {
                                error: response['error'] && response['error']['message']
                            }
                        });
                    }
                    return throwError(response['error'] && response['error']['message'] || 'Unknown error!');
                })
            );
    }

    put(url: string, body: any = null, auth: boolean = true, params = null) {
        if (auth) {
            this.headers = new HttpHeaders({
                'Authorization': 'Bearer '
                    + this.authenticationService.token, 'Content-Type': 'application/json'
            });
        } else {
            this.headers = new HttpHeaders({'Content-Type': 'application/json'});
        }
        return this.http.put(this.apiUrl + url, body, {headers: this.headers, params: params})
            .pipe(
                map((response: Response) => response['message']),
                catchError((response: Response) => {
                    if (response['status_code'] === 401 || response['error']['status_code'] === 401 ||
                        response['status_code'] === 403 || response['error']['status_code'] === 403) {
                        this.authenticationService.logout();
                        this.router.navigate(['login'], {
                            state: {
                                error: response['error'] && response['error']['message']
                            }
                        });
                    }
                    return throwError(response['error'] && response['error']['message'] || 'Unknown error!');
                })
            );
    }

    delete(url: string, auth: boolean = true, params = null) {
        if (auth) {
            this.headers = new HttpHeaders({
                'Authorization': 'Bearer '
                    + this.authenticationService.token, 'Content-Type': 'application/json'
            });
        } else {
            this.headers = new HttpHeaders({'Content-Type': 'application/json'});
        }
        return this.http.delete(this.apiUrl + url, {headers: this.headers, params: params})
            .pipe(
                map((response: Response) => response['message']),
                catchError((response: Response) => {
                    if (response['status_code'] === 401 || response['error']['status_code'] === 401 ||
                        response['status_code'] === 403 || response['error']['status_code'] === 403) {
                        this.authenticationService.logout();
                        this.router.navigate(['login'], {
                            state: {
                                error: response['error'] && response['error']['message']
                            }
                        });
                    }
                    return throwError(response['error'] && response['error']['message'] || 'Unknown error!');
                })
            );
    }

}
