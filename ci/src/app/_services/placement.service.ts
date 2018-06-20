import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

import { HttpService } from './http.service';

@Injectable()
export class PlacementService {

	constructor(private http: HttpService) { }

	getPlacementQuestions() {
	    return this.http.get('/placement');
	}

	getFirstTopicId(unitId) {
		return this.http.get('/placement/getTopicId/'+unitId);
	}

	doneUnit(unitId) {
		let request = { unit_id: unitId };
		return this.http.post('/placement/done-unit', request);
	}

	doneHalfUnit(unitId) {
		let request = { unit_id: unitId };
		return this.http.post('/placement/done-half-unit', request);
	}

}