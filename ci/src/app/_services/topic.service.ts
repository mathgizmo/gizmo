import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';

import { AuthenticationService } from './index';
import { ServerService } from './index';

@Injectable()
export class TopicService {
    constructor(
        private serverService: ServerService) {
    }

    getTopics() {
        // get users from api
        return this.serverService.get('/topic')
            .map((response: Response) => response);
    }
}