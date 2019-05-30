import { Injectable } from '@angular/core';

import { HttpService } from './http.service';

@Injectable()
export class TrackingService {
    constructor(
        private http: HttpService) {
    }

    startLesson(lesson_id) {
        // notify api about lesson start
        if (lesson_id === -1) {
          /** TODO: change this HARDCODED value to testoutstart! */
          // return this.http.post('/testoutstart', '')
          return this.http.post('/', '');
        } else {
            return this.http.post('/lesson/' + lesson_id + '/start', '');
        }
    }

    doneLesson(topic_id, lesson_id, start_datetime, weak_questions) {
        // notify api about lesson done
        const request = { start_datetime: start_datetime,
            weak_questions: weak_questions };
        if (lesson_id === -1) {
          return this.http.post('/topic/' + topic_id + '/testoutdone', request);
        } else {
          return this.http.post('/lesson/' + lesson_id + '/done', request);
        }
    }
}
