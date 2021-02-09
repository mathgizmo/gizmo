import {Injectable} from '@angular/core';
import {map, catchError} from 'rxjs/operators';

import {HttpService} from './http.service';

@Injectable()
export class AssignmentService {

    constructor(
        private http: HttpService) {
    }

    public getAssignments() {
        return this.http.get('/assignments')
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

    public addAssignment(item) {
        return this.http.post('/assignments/', item)
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

    public updateAssignment(app_id, item) {
        return this.http.put('/assignments/' + app_id, item)
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

    public deleteAssignment(app_id) {
        return this.http.delete('/assignments/' + app_id)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public copyAssignment(app_id) {
        return this.http.post('/assignments/' + app_id + '/copy')
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

    public getAppTree(app_id = 0) {
        return this.http.get('/assignments/' + app_id + '/tree')
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

}
