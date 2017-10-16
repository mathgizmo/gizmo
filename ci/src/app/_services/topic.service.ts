import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';

import { ServerService } from './server.service';

@Injectable()
export class TopicService {
    constructor(
        private serverService: ServerService) {
    }

    getTopics() {
        // get topic from api
        return this.serverService.get('/topic')
            .map((response: Response) => response);
    }

    getTopic(id) {
        // get topic from api
        return this.serverService.get('/topic/'+id)
            .map((response: Response) => response);
    }

    getLesson(topic_id, lesson_id) {
        // get lesson from api
        return this.serverService.get('/topic/'+topic_id+'/lesson/'+lesson_id)
            .map((response: Response) => response);
    }
}