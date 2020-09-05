import { Injectable } from '@angular/core';

import { HttpService } from './http.service';

@Injectable()
export class ContentService {

    constructor(
        private http: HttpService) {
    }

    getContent() {
        return this.http.get('/content');
    }
}
