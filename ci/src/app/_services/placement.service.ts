import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map';

import { HttpService } from './http.service';

@Injectable()
export class PlacementService {

	constructor(private http: HttpService) { }

	getPlacementQuestions() {
	    return this.http.get('/placement')
	        .map((response: Response) => response);
	}

	getFirstTopicId(unitId) {
		return this.http.get('/placement/getTopicId/'+unitId)
	        .map((response: Response) => response);
	}

	doneUnit(unitId) {
		let request = JSON.stringify({ unit_id: unitId });
		return this.http.post('/placement/done-unit', request)
	        .map((response: Response) => response);
	}

	doneHalfUnit(unitId) {
		let request = JSON.stringify({ unit_id: unitId });
		return this.http.post('/placement/done-half-unit', request)
	        .map((response: Response) => response);
	}

}