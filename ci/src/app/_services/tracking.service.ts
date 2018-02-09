import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';

import { ServerService } from './server.service';

@Injectable()
export class TrackingService {
    constructor(
        private serverService: ServerService) {
    }

    startLesson(lesson_id) {
        // notify api about lesson start
        if(lesson_id == -1) {
          // todo: change this request
          return this.serverService.post('/lesson/116/start', '')
          //return this.serverService.post('/testoutstart', '')
            .map((response: Response) => response); 
        }
        else {
        return this.serverService.post('/lesson/'+lesson_id+'/start', '')
            .map((response: Response) => response);
        }
    }

    doneLesson(topic_id, lesson_id, start_datetime, weak_questions) {
        // notify api about lesson done
        let request = JSON.stringify({ start_datetime: start_datetime, weak_questions: weak_questions });
        if(lesson_id == -1) {
          return this.serverService.post('/topic/'+topic_id+'/testoutdone', request)
            .map((response: Response) => response); 
        }
        else {
          return this.serverService.post('/lesson/'+lesson_id+'/done', request)
            .map((response: Response) => response);
        }
    }
}