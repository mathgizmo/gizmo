﻿import {Injectable} from '@angular/core';
import {map, catchError} from 'rxjs/operators';

import {HttpService} from './http.service';

@Injectable()
export class TestService {

    constructor(
        private http: HttpService) {
    }

    public getTest(test_id) {
        return this.http.get('/tests/' + test_id)
            .pipe(
                map((response: Response) => {
                    return response['item'];
                }),
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public getTests() {
        return this.http.get('/tests')
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

    public addTest(item) {
        return this.http.post('/tests/', item)
            .pipe(
                map((response: Response) => {
                    return response['item'];
                }),
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public copyTest(app_id) {
        return this.http.post('/tests/' + app_id + '/copy')
            .pipe(
                map((response: Response) => {
                    return response['item'];
                }),
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public updateTest(app_id, item) {
        return this.http.put('/tests/' + app_id, item)
            .pipe(
                map((response: Response) => {
                    return response['item'];
                }),
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public deleteTest(app_id) {
        return this.http.delete('/tests/' + app_id)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public trackTest(app_id) {
        return this.http.post('/tests/' + app_id + '/track', null, true, {
            hideLoader: true
        }).pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public getAppTree(app_id = 0) {
        return this.http.get('/tests/' + app_id + '/tree')
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

    public getAvailableIcons() {
        return this.http.get('/available-icons')
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

    public startTest(testId) {
        return this.http.post('/tests/' + testId + '/start');
    }

    public finishTest(testId) {
        return this.http.post('/tests/' + testId + '/finish');
    }

    public getQuestionsCount(tree, questions_per_lesson = 1) {
        return this.http.post('/tests/get-questions-count', { tree: tree, questions_per_lesson: questions_per_lesson})
            .pipe(
                map((response: Response) => {
                    return response['questions_count'];
                }),
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

}
