import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {BehaviorSubject, Observable} from 'rxjs';
import {catchError, map} from 'rxjs/operators';
import {environment} from '../../environments/environment';
import {User} from '../_models/index';

@Injectable()
export class AuthenticationService {
    public token: string;

    private userSubject: BehaviorSubject<User>;
    public user: Observable<User>;

    private readonly apiUrl = environment.apiUrl;
    private readonly baseUrl = environment.baseUrl;
    private headers?: HttpHeaders;

    constructor(private http: HttpClient) {
        this.token = localStorage.getItem('token');
        this.headers = new HttpHeaders({'Content-Type': 'application/json'});
        const user = JSON.parse(localStorage.getItem('user'));
        this.userSubject = new BehaviorSubject<User>(user);
        this.user = this.userSubject.asObservable();
    }

    public get userValue(): User {
        return this.userSubject.value;
    }

    login(username: string, password: string, captcha_response = null, tokenStr = null, ignoreCaptcha = false): Observable<any> {
        const ignoreCaptchaKey = ignoreCaptcha ? (environment.captchaKey || '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI') : null;
        const request = {email: username, password: password, token: tokenStr,
            'g-recaptcha-response': captcha_response, 'ignore-captcha-key': ignoreCaptchaKey};
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
                        localStorage.setItem('app_id', app_id + '');
                        return user;
                    } else {
                        return false;
                    }
                }),
                catchError((response: Response) => {
                    return response['message'];
                }),
            );
    }

    register(username: string, email: string, password: string,
             first_name: string = null, last_name: string = null,
             role: string = 'student', country_id: number = 1,
             captcha_response = null, ignoreCaptcha = false): Observable<any> {
        return this.http.post(this.apiUrl + '/register', {
            email: email,
            name: username,
            password: password,
            first_name: first_name,
            last_name: last_name,
            role: role,
            country_id: country_id,
            'g-recaptcha-response': captcha_response,
            'ignore-captcha-key': ignoreCaptcha ? (environment.captchaKey || '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI') : null
        }, {
            headers: this.headers
        });
    }

    logout() {
        if (this.token) {
            this.http.get(this.apiUrl + '/logout', {headers: new HttpHeaders({
                    'Authorization': 'Bearer ' + this.token, 'Content-Type': 'application/json'
                })}).subscribe(res => {
                this.token = null;
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                localStorage.removeItem('app_id');
                this.userSubject.next(null);
                return true;
            }, error => {
                this.token = null;
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                localStorage.removeItem('app_id');
                this.userSubject.next(null);
                return false;
            });
        } else {
            this.token = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('app_id');
            this.userSubject.next(null);
            return false;
        }

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
