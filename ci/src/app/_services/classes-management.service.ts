import {Injectable} from '@angular/core';
import {map, catchError} from 'rxjs/operators';
import {HttpService} from './http.service';

import {BehaviorSubject, Observable} from 'rxjs';
import {ClassModel} from '../_models';
import {HttpParams} from '@angular/common/http';

@Injectable()
export class ClassesManagementService {

    private readonly _classes = new BehaviorSubject<ClassModel[]>([]);
    readonly classes$ = this._classes.asObservable();

    get classes(): ClassModel[] {
        return this._classes.getValue();
    }

    set classes(val: ClassModel[]) {
        this._classes.next(val);
        localStorage.setItem('classes', JSON.stringify(this.classes));
    }

    constructor(
        private http: HttpService) {
        if (localStorage.hasOwnProperty('classes')) {
            this.classes = JSON.parse(localStorage.getItem('classes'));
        }
    }

    public getClasses() {
        return this.http.get('/classes')
            .pipe(
                map((response: Response) => {
                    this.classes = response['items'];
                    return response['items'];
                })
            );
    }

    public addClass(item) {
        this.classes = [
            ...this.classes,
            item
        ];
        return this.http.post('/classes/', item)
            .pipe(
                map((response: Response) => {
                    return response['item'];
                })
            );
    }

    public updateClass(class_id, item) {
        this.classes = this.classes.map(x => {
            if (x.id === class_id) {
                return item;
            } else {
                return x;
            }
        });
        return this.http.put('/classes/' + class_id, item)
            .pipe(
                map((response: Response) => {
                    return response['item'];
                })
            );
    }

    public deleteClass(class_id) {
        this.classes = this.classes.filter(item => item.id !== class_id);
        return this.http.delete('/classes/' + class_id);
    }

    public getStudents(class_id, extraData = true) {
        return this.http.get('/classes/' + class_id + '/students?extra=' + extraData)
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

    public addStudent(class_id, email) {
        return this.http.post('/classes/' + class_id + '/students', { email: email })
            .pipe(
                map((response: Response) => {
                    return response['item'];
                })
            );
    }

    public deleteStudent(class_id, student_id) {
        return this.http.delete('/classes/' + class_id + '/students/' + student_id);
    }

    public getAssignments(class_id) {
        return this.http.get('/classes/' + class_id + '/assignments');
    }

    public changeAssignment(class_id, item) {
        return this.http.put('/classes/' + class_id + '/assignments/' + item.id, item);
    }

    public changeAssignmentStudents(class_id, app_id, students = null) {
        return this.http.put('/classes/' + class_id + '/assignments/' + app_id + '/students', { students: students });
    }

    public addAssignmentToClass(class_id, app_id, students = null) {
        return this.http.post('/classes/' + class_id + '/assignments/' + app_id, { students: students })
            .pipe(
                map((response: Response) => {
                    return response['item'];
                })
            );
    }

    public deleteAssignmentFromClass(class_id, app_id) {
        return this.http.delete('/classes/' + class_id + '/assignments/' + app_id);
    }

    public getReport(class_id) {
        return this.http.get('/classes/' + class_id + '/report');
    }

    public getToDos(class_id) {
        return this.http.get('/classes/' + class_id + '/todo')
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

    getAnswersStatistics(class_id, student_id = null, app_id = null, date_from = null, date_to = null) {
        return this.http.get( '/classes/' + class_id + '/answers-statistics',
            true, {
                student_id: student_id ? student_id : '',
                app_id: app_id ? app_id : '',
                date_from: date_from ? date_from : '',
                date_to: date_to ? date_to : ''
            })
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

}
