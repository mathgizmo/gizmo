import {Injectable} from '@angular/core';
import {map, catchError} from 'rxjs/operators';

import {HttpService} from './http.service';

@Injectable()
export class ClassesManagementService {

    constructor(
        private http: HttpService) {
    }

    public getClasses() {
        return this.http.get('/classes')
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

    public addClass(item) {
        return this.http.post('/classes/', item)
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

    public updateClass(class_id, item) {
        return this.http.put('/classes/' + class_id, item)
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

    public deleteClass(class_id) {
        return this.http.delete('/classes/' + class_id)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public getStudents(class_id) {
        return this.http.get('/classes/' + class_id + '/students')
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

    public getAssignments(class_id) {
        return this.http.get('/classes/' + class_id + '/assignments')
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public changeAssignment(class_id, item) {
        return this.http.put('/classes/' + class_id + '/assignments/' + item.id, item)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public addAssignmentToClass(class_id, app_id) {
        return this.http.post('/classes/' + class_id + '/assignments/' + app_id)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

    public deleteAssignmentFromClass(class_id, app_id) {
        return this.http.delete('/classes/' + class_id + '/assignments/' + app_id)
            .pipe(
                catchError(error => {
                    console.log(error);
                    throw Error(error);
                })
            );
    }

}
