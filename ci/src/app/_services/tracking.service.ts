import { Injectable } from '@angular/core';

import { HttpService } from './http.service';

@Injectable()
export class TrackingService {

    private appId = +localStorage.getItem('app_id');

    constructor(
        private http: HttpService) {
    }

    startLesson(lesson_id) {
        let url = '/lesson/' + lesson_id + '/start';
        if (this.appId) {
            url += '?app_id=' + this.appId;
        }
        if (lesson_id !== -1) {
            return this.http.post(url, '');
        } else {
            return this.http.post('/', ''); // return this.http.post('/testoutstart', '')
        }
    }

    doneLesson(topic_id, lesson_id, start_datetime, weak_questions) {
        const request = { start_datetime: start_datetime,
            weak_questions: weak_questions };
        if (lesson_id === -1) {
            let url = '/topic/' + topic_id + '/testout/done';
            if (this.appId) {
                url += '?app_id=' + this.appId;
            }
            return this.http.post(url, request);
        } else {
            let url = '/lesson/' + lesson_id + '/done';
            if (this.appId) {
                url += '?app_id=' + this.appId;
            }
            return this.http.post(url, request);
        }
    }

    finishTestout(topic_id, lesson_id, start_datetime, weak_questions) {
        const request = { lesson_id: lesson_id, start_datetime: start_datetime,
            weak_questions: weak_questions };
        let url = '/topic/' + topic_id + '/testout/done-lessons';
        if (this.appId) {
            url += '?app_id=' + this.appId;
        }
        return this.http.post(url, request);
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

    trackQuestionAnswer(question_id, is_right_answer) {
        let url = '/question/' + question_id + '/tracking';
        if (this.appId) {
            url += '?app_id=' + this.appId;
        }
        return this.http.post(url, {'is_right_answer': is_right_answer});
    }
}
