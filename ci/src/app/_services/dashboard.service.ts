import {Injectable} from '@angular/core';

import {HttpService} from './http.service';

@Injectable()
export class DashboardService {

    constructor(private http: HttpService) {
    }

    getDashboards() {
        return this.http.get('/dashboard');
    }

}
