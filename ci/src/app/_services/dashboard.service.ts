import {Injectable} from '@angular/core';

import {HttpService} from './http.service';

@Injectable()
export class DashboardService {

    constructor(private http: HttpService) {
    }

    getDashboards() {
        return this.http.get('/dashboard');
    }

    getAdSettings(classId?: number, assignmentId?: number) {
        const params: any = {};
        if (classId) params.class_id = classId;
        if (assignmentId) params.assignment_id = assignmentId;
        return this.http.get('/ad-settings', true, params);
    }
    
}
