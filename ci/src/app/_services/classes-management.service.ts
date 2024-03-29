﻿import {Injectable} from '@angular/core';
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
        } else {
            this.getClasses().subscribe(() => {});
        }
    }

    public getClasses(filters = null) {
        let url = '/classes';
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.get(url)
            .pipe(
                map((response: Response) => {
                    this.classes = response['items'];
                    return response['items'];
                })
            );
    }

    public getResearchClasses(filters = null) {
        let url = '/research-classes';
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.get(url)
            .pipe(
                map((response: Response) => {
                    this.classes = response['items'];
                    return response['items'];
                })
            );
    }

    public getClass(classId) {
        return this.http.get('/classes/' + classId)
            .pipe(
                map((response: Response) => {
                    return response['item'];
                }),
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

    public getStudents(class_id, withExtra = false, filters = null) {
        let url = '/classes/' + class_id + '/students?extra=' + withExtra;
        if (filters) {
            url += '&' + new URLSearchParams(filters).toString();
        }
        return this.http.get(url)
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

    public changeStudent(class_id, item) {
        return this.http.put('/classes/' + class_id + '/students/' + item.id, item);
    }

    public deleteStudent(class_id, student_id) {
        return this.http.delete('/classes/' + class_id + '/students/' + student_id);
    }

    public addStudent(class_id, student_id) {
        return this.http.post('/classes/' + class_id + '/students/' + student_id + '/subscribe');
    }

    public getStudentAssignmentsReport(class_id, student_id) {
        return this.http.get('/classes/' + class_id + '/students/' + student_id + '/report/assignments')
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

    public getStudentTestsReport(class_id, student_id) {
        return this.http.get('/classes/' + class_id + '/students/' + student_id + '/report/tests')
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

    public getTeachers(class_id, filters = null) {
        let url = '/classes/' + class_id + '/teachers';
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.get(url);
    }

    public addTeacher(class_id, teacher_id, body = null) {
        return this.http.post('/classes/' + class_id + '/teachers/' + teacher_id, body)
            .pipe(
                map((response: Response) => {
                    return response['item'];
                })
            );
    }

    public changeTeacher(class_id, item) {
        return this.http.put('/classes/' + class_id + '/teachers/' + item.id, item);
    }

    public deleteTeacher(class_id, teacher_id) {
        return this.http.delete('/classes/' + class_id + '/teachers/' + teacher_id);
    }

    public getAssignments(class_id, filters = null) {
        let url = '/classes/' + class_id + '/assignments';
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.get(url);
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

    public getTests(class_id, filters = null) {
        let url = '/classes/' + class_id + '/tests';
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.get(url);
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

    public getTestReport(class_id, app_id, filters = null) {
        let url = '/classes/' + class_id + '/tests/' + app_id + '/report';
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.get(url);
    }

    public downloadTestReportPDF(class_id, app_id, student_id): Observable<Blob> {
        return this.http.download('/classes/' + class_id + '/tests/' + app_id + '/student/' + student_id + '/report.pdf');
    }

    /* public downloadPoorQuestionsPDF(class_id, app_id): Observable<Blob> {
        return this.http.download('/classes/' + class_id + '/tests/' + app_id + '/poor-questions-report.pdf');
    } */

    public downloadTestsReport(class_id, format = 'csv', filters = null) {
        let url = '/classes/' + class_id + '/tests-report.' + format;
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.download(url);
    }

    public downloadAssignmentsReport(class_id, format = 'csv', filters = null) {
        let url = '/classes/' + class_id + '/assignments-report.' + format;
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.download(url);
    }

    public downloadStudents(class_id, format = 'csv') {
        return this.http.download('/classes/' + class_id + '/students.' + format);
    }

    public resetTestProgress(class_id, app_id, student_id, attempt_id = null) {
        return this.http.post('/classes/' + class_id + '/tests/' + app_id + '/student/' + student_id + '/reset', {
            attempt_id: attempt_id
        });
    }

    public getTestDetails(class_id, app_id, student_id, attempt_id = null) {
        return this.http.get('/classes/' + class_id + '/tests/' + app_id + '/student/' + student_id + '/details?attempt_id=' + attempt_id);
    }

    public getReport(class_id, filters = null) {
        let url = '/classes/' + class_id + '/report';
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.get(url);
    }

    public getAnswersStatistics(class_id, student_id = null, app_id = null,
        date_from = null, date_to = null, type = 'assignment', for_research = false) {
        return this.http.get( '/classes/' + class_id + '/answers-statistics',
            true, {
                student_id: student_id ? student_id : '',
                app_id: app_id ? app_id : '',
                date_from: date_from ? date_from : '',
                date_to: date_to ? date_to : '',
                type: type,
                for_research: for_research ? 1 : 0
            })
            .pipe(
                map((response: Response) => {
                    return response['items'];
                })
            );
    }

}
