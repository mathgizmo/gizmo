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
        let url = '/topic';
        if (this.appId) {
            url += '?app_id=' + this.appId;
        }
        return this.http.get(url);
    }

    // get topic from api
    getTopic(id) {
        let url = '/topic/' + id;
        if (this.appId) {
            url += '?app_id=' + this.appId;
        }
        return this.http.get(url);
    }

    // get lesson from api
    getLesson(topic_id, lesson_id, from_content_review = false) {
        let url = '/topic/' + topic_id;
        if (lesson_id === -1) {
            url += '/testout';
        } else {
            url += '/lesson/' + lesson_id;
        }
        if (this.appId) {
            url += '?app_id=' + (from_content_review ? 0 : this.appId);
        }
        return this.http.get(url);
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
