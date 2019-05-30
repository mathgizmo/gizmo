import {Injectable} from '@angular/core';
import {map, catchError} from 'rxjs/operators';

import {User} from '../_models/index';
import {HttpService} from './http.service';

@Injectable()
export class UserService {

    constructor(
        private http: HttpService) {
    }

    public getProfile() {
        return this.http.get('/profile')
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public changeProfile(user: User) {
        const request = {
            name: user.username,
            email: user.email,
            question_num: user.questionNum
        };
        return this.http.post('/profile', request)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public changePassword(newPassword: string, confirmedPassword: string) {
        const request = {
            password: newPassword,
            confirm_password: confirmedPassword
        };
        return this.http.post('/profile', request)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }
}
