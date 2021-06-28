import {Injectable} from '@angular/core';

import {HttpService} from './http.service';

@Injectable()
export class TutorialService {

    constructor(private http: HttpService) {
    }

    getTutorials() {
        return this.http.get('/tutorial');
    }

}
