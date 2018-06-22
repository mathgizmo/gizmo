import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

import { HttpService } from './http.service';

@Injectable()
export class TopicService {
    
    constructor(
        private http: HttpService) {
    }

    // get topics from api
    getTopics() {
        return this.http.get('/topic');
    }

    // get topic from api
    getTopic(id) {
        return this.http.get('/topic/'+id);
    }

    // get lesson from api
    getLesson(topic_id, lesson_id) {
        if(lesson_id == -1) {
            return this.http.get('/topic/'+topic_id+'/testout');
        }
        else {
          return this.http.get('/topic/'+topic_id+'/lesson/'+lesson_id);
        }
    }

    // notify api about question error
    reportError(question_id, answers, option, custom) {
        let request = { answers: answers, options: option, comment: custom };
        return this.http.post('/report_error/'+question_id, request);
    }
}