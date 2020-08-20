import {Injectable} from '@angular/core';

import {HttpService} from './http.service';

@Injectable()
export class CountryService {

    constructor(private http: HttpService) {
    }

    getCountries() {
        return this.http.get('/countries');
    }

}
