import {Injectable} from '@angular/core';

import {HttpService} from './http.service';

@Injectable()
export class SettingsService {

    constructor(private http: HttpService) {
    }

    getSetting(key) {
        return this.http.get('/settings/' + key);
    }

    getWelcomeTexts() {
        return this.http.get('/welcome');
    }

}
