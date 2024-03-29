﻿import {Injectable} from '@angular/core';
import {map} from 'rxjs/operators';

import {User} from '../_models/index';
import {HttpService} from './http.service';

@Injectable()
export class UserService {

    constructor(
        private http: HttpService) {
    }

    public getProfile() {
        return this.http.get('/profile');
    }

    public changeProfile(user: User) {
        const request = {
            first_name: user.first_name,
            last_name: user.last_name,
            email: user.email,
            country_id: user.country_id,
            role: user.role
        };
        return this.http.post('/profile', request);
    }

    public changePassword(newPassword: string, confirmedPassword: string) {
        const request = {
            password: newPassword,
            confirm_password: confirmedPassword
        };
        return this.http.post('/profile', request);
    }

    public clearRedirectTo() {
        return this.http.post('/profile/clear-redirect-to');
    }

    public changeApplication(appId: number) {
        const request = {
            app_id: appId,
        };
        return this.http.post('/profile/app/', request);
    }

    public changeOptions(options: any) {
        return this.http.post('/profile/options', options);
    }

    public getToDos(classId = null) {
        return this.http.get('/profile/todo?class_id=' + classId)
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

    public getTests(classId = null) {
        return this.http.get('/profile/tests?class_id=' + classId)
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

    public revealTest(test_id, password) {
        return this.http.post('/profile/tests/' + test_id + '/reveal', {password: password});
    }

    public getClasses() {
        return this.http.get('/profile/classes');
    }

    public getClass(classId) {
        return this.http.get('/profile/classes/' + classId)
            .pipe(
                map((response: Response) => {
                    return response['item'];
                }),
            );
    }

    public updateClassConsent(classId, data) {
        return this.http.post('/profile/classes/' + classId + '/consent', data);
    }

    public subscribeClass(classId: number|string) {
        const request = {
            class_id: classId,
        };
        return this.http.post('/profile/classes/' + classId + '/subscribe', request)
            .pipe(
                map((response: Response) => {
                    return response['item'];
                }),
            );
    }

    public unsubscribeClass(classId: number) {
        const request = {
            class_id: classId,
        };
        return this.http.post('/profile/classes/' + classId + '/unsubscribe', request);
    }

    public downloadAssignmentsReport(class_id, format = 'csv') {
        return this.http.download('/profile/classes/' + class_id + '/assignments-report.' + format);
    }

    public downloadTestsReport(class_id, format = 'csv') {
        return this.http.download('/profile/classes/' + class_id + '/tests-report.' + format);
    }
}
