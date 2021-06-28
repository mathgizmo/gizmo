import {Injectable} from '@angular/core';

import {HttpService} from './http.service';

@Injectable()
export class FaqService {

    constructor(private http: HttpService) {
    }

    getFaqs() {
        return this.http.get('/faq');
    }

}
