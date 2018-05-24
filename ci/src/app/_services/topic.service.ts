import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

import { HttpService } from './http.service';

@Injectable()
export class TopicService {
    
    constructor(
        private http: HttpService) {
    }

    getTopics() {
        // get topic from api
        return this.http.get('/topic')
            .pipe(
                map((response: Response) => response)
            );
    }

    getTopic(id) {
        // get topic from api
        return this.http.get('/topic/'+id)
            .pipe(
                map((response: Response) => response)
            );
    }

    getLesson(topic_id, lesson_id) {
        // get lesson from api
        if(lesson_id == -1) {
            return this.http.get('/topic/'+topic_id+'/testout')
                .pipe(
                    map((response: Response) => response)
                );
        }
        else {
          return this.http.get('/topic/'+topic_id+'/lesson/'+lesson_id)
            .pipe(
                map((response: Response) => response)
            );
        }
    }

    reportError(question_id, answers, option, custom) {
        // notify api about question error
        let request = JSON.stringify({ answers: answers, 
            options: option, comment: custom });
        return this.http.post('/report_error/'+question_id, request)
            .pipe(
                map((response: Response) => response)
            );
    }
}