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
        return this.http.post('/classes/', item)
            .pipe(
                map((response: Response) => {
                    const classItem = response['item'];
                    this.classes = [
                        ...this.classes,
                        classItem
                    ];
                    return classItem;
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

    public emailClass(class_id, mail) {
        return this.http.post('/classes/' + class_id + '/email', mail);
    }

    public getStudents(class_id, extraData = true) {
        return this.http.get('/classes/' + class_id + '/students?extra=' + extraData)
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

    public addStudents(class_id, emails) {
        return this.http.post('/classes/' + class_id + '/students', { email: emails })
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

    public deleteStudent(class_id, student_id) {
        return this.http.delete('/classes/' + class_id + '/students/' + student_id);
    }

    public changeStudent(class_id, item) {
        return this.http.put('/classes/' + class_id + '/students/' + item.id, item);
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

    public getTests(class_id) {
        return this.http.get('/classes/' + class_id + '/tests');
    }

    public changeTest(class_id, item) {
        return this.http.put('/classes/' + class_id + '/tests/' + item.id, item);
    }

    public changeTestStudents(class_id, app_id, students = null) {
        return this.http.put('/classes/' + class_id + '/tests/' + app_id + '/students', { students: students });
    }

    public addTestToClass(class_id, app_id, students = null) {
        return this.http.post('/classes/' + class_id + '/tests/' + app_id, { students: students })
            .pipe(
                map((response: Response) => {
                    return response['item'];
                })
            );
    }

    public deleteTestFromClass(class_id, app_id) {
        return this.http.delete('/classes/' + class_id + '/tests/' + app_id);
    }

    public getTestReport(class_id, app_id) {
        return this.http.get('/classes/' + class_id + '/tests/' + app_id + '/report');
    }

    public downloadTestReportPDF(class_id, app_id, student_id): Observable<Blob> {
        return this.http.download('/classes/' + class_id + '/tests/' + app_id + '/student/' + student_id + '/report.pdf');
    }

    public downloadTestsReport(class_id, format = 'csv') {
        return this.http.download('/classes/' + class_id + '/tests-report.' + format);
    }

    public downloadAssignmentsReport(class_id, format = 'csv') {
        return this.http.download('/classes/' + class_id + '/assignments-report.' + format);
    }

    public resetTestProgress(class_id, app_id, student_id, attempt_id = null) {
        return this.http.post('/classes/' + class_id + '/tests/' + app_id + '/student/' + student_id + '/reset', {
            attempt_id: attempt_id
        });
    }

    public getTestDetails(class_id, app_id, student_id, attempt_id = null) {
        return this.http.get('/classes/' + class_id + '/tests/' + app_id + '/student/' + student_id + '/details?attempt_id=' + attempt_id);
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

    getAnswersStatistics(class_id, student_id = null, app_id = null, date_from = null, date_to = null, type = 'assignment') {
        return this.http.get( '/classes/' + class_id + '/answers-statistics',
            true, {
                student_id: student_id ? student_id : '',
                app_id: app_id ? app_id : '',
                date_from: date_from ? date_from : '',
                date_to: date_to ? date_to : '',
                type: type
            })
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

}
