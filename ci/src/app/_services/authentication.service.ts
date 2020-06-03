import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {BehaviorSubject, Observable} from 'rxjs';
import {map} from 'rxjs/operators';
import {environment} from '../../environments/environment';
import {User} from '../_models/user';

@Injectable()
export class AuthenticationService {
    public token: string;

    private userSubject: BehaviorSubject<User>;
    public user: Observable<User>;

    private readonly apiUrl = environment.apiUrl;
    private readonly baseUrl = environment.baseUrl;
    private headers?: HttpHeaders;

    constructor(private http: HttpClient) {
        // set token if saved in local storage
        this.token = localStorage.getItem('token');
        this.headers = new HttpHeaders({'Content-Type': 'application/json'});

        const user = JSON.parse(localStorage.getItem('user'));
        this.userSubject = new BehaviorSubject<User>(user);
        this.user = this.userSubject.asObservable();
    }

    public get userValue(): User {
        return this.userSubject.value;
    }

    login(username: string, password: string): Observable<any> {
        const request = {email: username, password: password};
        return this.http.post(this.apiUrl + '/authenticate', request, {headers: this.headers})
            .pipe(
                map((response: Response) => {
                    // login successful if there's a jwt token in the response
                    const user = response && response['message'] && response['message']['user'] && JSON.parse(response['message']['user']);
                    const app_id = response && response['message'] && response['message']['app_id'];
                    const token = response && response['message'] && response['message']['token'];
                    if (token) {
                        // set token property
                        this.token = token;
                        // store username and jwt token in local storage to keep user logged in between page refreshes
                        localStorage.setItem('token', token);
                        localStorage.setItem('user', JSON.stringify(user));
                        this.userSubject.next(user);
                        let question_num = 3;
                        if (user.question_num !== undefined) {
                            question_num = user.question_num;
                        }
                        localStorage.setItem('question_num', question_num + '');
                        localStorage.setItem('app_id', app_id + '');
                        // return true to indicate successful login
                        return true;
                    } else {
                        // return false to indicate failed login
                        return false;
                    }
                })
            );
    }

    register(username: string, email: string, password: string, first_name: string = null, last_name: string = null): Observable<any> {
        const request = {email: email, name: username, password: password, first_name: first_name, last_name: last_name};
        return this.http.post(this.apiUrl + '/register', request, {headers: this.headers})
            .pipe(
                map((response: Response) => {
                    // login successful if there's a jwt token in the response
                    this.token = response && response['message'] && response['message']['token'];
                    return response;
                })
            );
    }

    logout(): void {
        this.token = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.userSubject.next(null);
    }

    sendPasswordResetEmail(email: string): Observable<any> {
        const url = this.baseUrl + '/reset-password';
        const request = {email: email, url: url};
        return this.http.post(this.apiUrl + '/password-reset-email', request, {headers: this.headers})
            .pipe(
                map((response: Response) => {
                    return response;
                })
            );
    }

    resetPassword(newPassword: string, confirmedPassword: string, token: string): Observable<any> {
        const request = {password: newPassword, confirm_password: confirmedPassword, token: token};
        return this.http.post(this.apiUrl + '/reset-password', request, {headers: this.headers})
            .pipe(
                map((response: Response) => {
                    return response;
                })
            );
    }
}
