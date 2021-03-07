import { Injectable } from '@angular/core';

import { HttpService } from './http.service';

@Injectable()
export class TrackingService {

    constructor(
        private http: HttpService) {
    }

    startLesson(lesson_id, assignmentId = -1) {
        let url = '/lesson/' + lesson_id + '/start';
        if (assignmentId > 0) {
            url += '?class_app_id=' + assignmentId;
        } else {
            const appId = (assignmentId === 0 ? 0 : +localStorage.getItem('app_id'));
            url += '?app_id=' + appId;
        }
        if (lesson_id !== -1) {
            return this.http.post(url, '');
        } else {
            return this.http.post('/', ''); // return this.http.post('/testoutstart', '')
        }
    }

    doneLesson(topic_id, lesson_id, start_datetime, weak_questions, assignmentId = -1) {
        const request = { start_datetime: start_datetime,
            weak_questions: weak_questions };
        let url = lesson_id === -1 ? ('/topic/' + topic_id + '/testout/done') : ('/lesson/' + lesson_id + '/done');
        if (assignmentId > 0) {
            url += '?class_app_id=' + assignmentId;
        } else {
            const appId = (assignmentId === 0 ? 0 : +localStorage.getItem('app_id'));
            url += '?app_id=' + appId;
        }
        return this.http.post(url, request);
    }

    finishTestout(topic_id, lesson_id, start_datetime, weak_questions, assignmentId = null) {
        const request = { lesson_id: lesson_id, start_datetime: start_datetime,
            weak_questions: weak_questions };
        let url = '/topic/' + topic_id + '/testout/done-lessons';
        if (assignmentId > 0) {
            url += '?class_app_id=' + assignmentId;
        } else {
            const appId = (assignmentId === 0 ? 0 : +localStorage.getItem('app_id'));
            url += '?app_id=' + appId;
        }
        return this.http.post(url, request);
    }

    trackQuestionAnswer(question_id, is_right_answer, class_app_id = null) {
        let url = '/question/' + question_id + '/tracking';
        if (class_app_id > 0) {
            url += '?class_app_id=' + class_app_id;
        } else {
            const appId = (class_app_id === 0 ? 0 : +localStorage.getItem('app_id'));
            url += '?app_id=' + appId;
        }
        return this.http.post(url, {'is_right_answer': is_right_answer});
    }

    getLastVisitedLesson(student_id) {
        return this.http.get('/lesson/last-visited/' + student_id);
    }

    getLastVisitedTopic(student_id) {
        return this.http.get('/topic/last-visited/' + student_id);
    }

    getLastVisitedUnit(student_id) {
        return this.http.get('/unit/last-visited/' + student_id);
    }
}
