import { Injectable } from '@angular/core';

import { HttpService } from './http.service';

@Injectable()
export class TrackingService {

    private appId = +localStorage.getItem('app_id');

    constructor(
        private http: HttpService) {
    }

    startLesson(lesson_id) {
        if (lesson_id !== -1) {
            return this.http.post('/lesson/' + lesson_id + '/start' + '?app_id=' + this.appId, '');
        } else {
            return this.http.post('/', ''); // return this.http.post('/testoutstart', '')
        }
    }

    doneLesson(topic_id, lesson_id, start_datetime, weak_questions) {
        const request = { start_datetime: start_datetime,
            weak_questions: weak_questions };
        if (lesson_id === -1) {
          return this.http.post('/topic/' + topic_id + '/testout/done' + '?app_id=' + this.appId, request);
        } else {
          return this.http.post('/lesson/' + lesson_id + '/done' + '?app_id=' + this.appId, request);
        }
    }

    finishTestout(topic_id, lesson_id, start_datetime, weak_questions) {
        const request = { lesson_id: lesson_id, start_datetime: start_datetime,
            weak_questions: weak_questions };
        return this.http.post('/topic/' + topic_id + '/testout/done-lessons' + '?app_id=' + this.appId, request);
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
