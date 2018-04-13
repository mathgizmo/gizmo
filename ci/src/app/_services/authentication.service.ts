import { Injectable } from '@angular/core';
import { Http, Headers, Response, RequestOptions } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';
import { environment } from '../../environments/environment';

@Injectable()
export class AuthenticationService {
    public token: string;
    private readonly apiUrl = environment.apiUrl;

    constructor(private http: Http) {
        // set token if saved in local storage
        var currentUser = JSON.parse(localStorage.getItem('currentUser'));
        this.token = currentUser && currentUser.token;
    }

    login(username: string, password: string): Observable<boolean> {
        let request = JSON.stringify({ email: username, password: password });

        let headers = new Headers({ 'Content-Type': 'application/json' }); // ... Set content type to JSON
        let options = new RequestOptions({ headers: headers }); // Create a request option

        return this.http.post(this.apiUrl+'/authenticate', request, options)
            .map((response: Response) => {
                // login successful if there's a jwt token in the response
                let token = response.json() && response.json().message && 
                    response.json().message.token;
                if (token) {
                    // set token property
                    this.token = token;

                    // store username and jwt token in local storage to keep user logged in between page refreshes
                    localStorage.setItem('currentUser', 
                        JSON.stringify({ username: username, token: token }));
                    var question_num = 5;
                    if (response.json().message && response.json()
                        .message.question_num != undefined) {
                        question_num = response.json().message.question_num;
                    }
                    localStorage.setItem('question_num', question_num+"");
                    // return true to indicate successful login
                    return true;
                } else {
                    // return false to indicate failed login
                    return false;
                }
            });
    }

    register(username: string, email: string, password: string): Observable<boolean> {
        let request = JSON.stringify({ email: email, name: username, password: password });

        let headers = new Headers({ 'Content-Type': 'application/json' }); // ... Set content type to JSON
        let options = new RequestOptions({ headers: headers }); // Create a request option

        return this.http.post(this.apiUrl+'/register', request, options)
            .map((response: Response) => {
                // login successful if there's a jwt token in the response
                let token = response.json() && response.json()
                    .message && response.json().message.token;
                return response.json();
            });
    }

    logout(): void {
        // clear token remove user from local storage to log user out
        this.token = null;
        localStorage.removeItem('currentUser');
    }

    sendPasswordResetEmail(email: string): Observable<boolean>  {
        let request = JSON.stringify({ email: email });
        let headers = new Headers({ 'Content-Type': 'application/json' }); // ... Set content type to JSON
        let options = new RequestOptions({ headers: headers }); // Create a request option
        return this.http.post(this.apiUrl+'/password-reset-email', request, options)
            .map((response: Response) => {
                return response.json();
            });
    }
}