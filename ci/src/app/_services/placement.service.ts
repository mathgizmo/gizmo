import {Injectable} from '@angular/core';

import {HttpService} from './http.service';

@Injectable()
export class PlacementService {

    constructor(private http: HttpService) {
    }

    getPlacementQuestions() {
        return this.http.get('/placement');
    }

    getFirstTopicId(unitId) {
        return this.http.get('/placement/getTopicId/' + unitId);
    }

    doneUnit(unitId) {
        const request = {unit_id: unitId};
        return this.http.post('/placement/done-unit', request);
    }

    doneHalfUnit(unitId) {
        const request = {unit_id: unitId};
        return this.http.post('/placement/done-half-unit', request);
    }

}
