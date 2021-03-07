import { Injectable } from '@angular/core';

import { HttpService } from './http.service';

@Injectable()
export class TopicService {
    constructor(
        private http: HttpService) {
    }

    // get topics from api
    getTopics(assignmentId = null) {
        let url = '/topic';
        if (assignmentId > 0) {
            url += '?class_app_id=' + assignmentId;
        } else {
            const appId = (assignmentId === 0 ? 0 : +localStorage.getItem('app_id')); // fix navigation to home from home
            url += '?app_id=' + appId;
        }
        return this.http.get(url);
    }

    // get topic from api
    getTopic(id, assignmentId = null) {
        let url = '/topic/' + id;
        if (assignmentId > 0) {
            url += '?class_app_id=' + assignmentId;
        } else {
            const appId = (assignmentId === 0 ? 0 : +localStorage.getItem('app_id'));
            url += '?app_id=' + appId;
        }
        return this.http.get(url);
    }

    // get lesson from api
    getLesson(topic_id, lesson_id, from_content_review = false, assignmentId = null) {
        let url = '/topic/' + topic_id;
        if (lesson_id === -1) {
            url += '/testout';
        } else {
            url += '/lesson/' + lesson_id;
        }
        if (assignmentId > 0) {
            url += '?class_app_id=' + (from_content_review ? 0 : assignmentId);
        } else {
            const appId = (assignmentId === 0 ? 0 : +localStorage.getItem('app_id'));
            url += '?app_id=' + (from_content_review ? 0 : appId);
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
