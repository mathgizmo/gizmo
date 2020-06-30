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
            first_name: user.first_name,
            last_name: user.last_name,
            email: user.email,
            question_num: user.question_num
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

    public changeApplication(appId: number) {
        const request = {
            app_id: appId,
        };
        return this.http.post('/profile/app/', request)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public getToDos() {
        return this.http.get('/profile/todo')
            .pipe(
                map((response: Response) => {
                    return response['items'];
                }),
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public getClasses() {
        return this.http.get('/profile/classes')
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public getClassInvitations() {
        return this.http.get('/profile/classes/invitations')
            .pipe(
                map((response: Response) => {
                    return response['items'];
                }),
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public subscribeClass(classId: number) {
        const request = {
            class_id: classId,
        };
        return this.http.post('/profile/classes/' + classId + '/subscribe', request)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public unsubscribeClass(classId: number) {
        const request = {
            class_id: classId,
        };
        return this.http.post('/profile/classes/' + classId + '/unsubscribe', request)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }
}
