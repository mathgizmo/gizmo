import { Injectable, Inject} from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../environments/environment';

@Injectable()
export class AuthenticationService {
    public token: string;
    private readonly apiUrl = environment.apiUrl;
    private readonly baseUrl = environment.baseUrl;
    private headers?: HttpHeaders;

    constructor(private http: HttpClient) {
        // set token if saved in local storage
        var currentUser = JSON.parse(localStorage.getItem('currentUser'));
        this.token = currentUser && currentUser.token;
        this.headers = new HttpHeaders({'Content-Type': 'application/json' });
    }

    login(username: string, password: string): Observable<any> {
        let request = { email: username, password: password };
        return this.http.post(this.apiUrl+'/authenticate', request, { headers: this.headers })
            .pipe(
                map((response: Response) => {
                    // login successful if there's a jwt token in the response
                    let token = response && response['message'] && 
                        response['message']['token'];
                    if (token) {
                        // set token property
                        this.token = token;    
                        // store username and jwt token in local storage to keep user logged in between page refreshes
                        localStorage.setItem('currentUser', 
                            JSON.stringify({ username: username, token: token }));
                        var question_num = 5;
                        if (response['message'] && response['message']['question_num'] != undefined) {
                            question_num = response['message']['question_num'];
                        }
                        localStorage.setItem('question_num', question_num+"");
                        // return true to indicate successful login
                        return true;
                    } else {
                        // return false to indicate failed login
                        return false;
                    }
                })
            );
    }

    register(username: string, email: string, password: string): Observable<any> {
        let request = { email: email, name: username, password: password };
        return this.http.post(this.apiUrl+'/register', request, { headers: this.headers } )
            .pipe(
                map((response: Response) => {
                    // login successful if there's a jwt token in the response
                    let token = response && response['message'] && response['message']['token'];
                    return response;
                })
            );
    }

    logout(): void {
        // clear token remove user from local storage to log user out
        this.token = null;
        localStorage.removeItem('currentUser');
    }

    sendPasswordResetEmail(email: string): Observable<any>  {
        let url = this.baseUrl+'/reset-password';
        let request = { email: email, url:  url };
        return this.http.post(this.apiUrl+'/password-reset-email', request, { headers: this.headers })
            .pipe(
                map((response: Response) => {
                    return response;
                })
            );
    }

    resetPassword(newPassword: string, confirmedPassword: string, token: string): Observable<any> {
        let request = { password: newPassword, confirm_password: confirmedPassword, token: token };
        return this.http.post(this.apiUrl+'/reset-password', request, { headers: this.headers })
            .pipe(
                map((response: Response) => {
                    return response;
                })
            );
    }
}