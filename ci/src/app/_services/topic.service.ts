import { Injectable } from '@angular/core';

import { HttpService } from './http.service';

@Injectable()
export class TopicService {

    private appId = +localStorage.getItem('app_id');

    constructor(
        private http: HttpService) {
    }

    // get topics from api
    getTopics() {
        this.appId = +localStorage.getItem('app_id'); // fix navigation to home from home
        return this.http.get('/topic' + '?app_id=' + this.appId);
    }

    // get topic from api
    getTopic(id) {
        return this.http.get('/topic/' + id + '?app_id=' + this.appId);
    }

    // get lesson from api
    getLesson(topic_id, lesson_id) {
        if (lesson_id === -1) {
            return this.http.get('/topic/' + topic_id + '/testout' + '?app_id=' + this.appId);
        } else {
          return this.http.get('/topic/' + topic_id + '/lesson/' + lesson_id + '?app_id=' + this.appId);
        }
    }

    // notify api about question error
    reportError(question_id, answers, option, custom) {
        const request = { is_feedback: false, answers: answers, options: option, comment: custom };
        return this.http.post('/report_error/' + question_id, request);
    }

    sendFeedback(question_id, text) {
        const request = { is_feedback: true, comment: text };
        return this.http.post('/report_error/' + question_id, request);
    }
}
