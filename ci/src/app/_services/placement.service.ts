import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

import { HttpService } from './http.service';

@Injectable()
export class PlacementService {

	constructor(private http: HttpService) { }

	getPlacementQuestions() {
	    return this.http.get('/placement')
	        .pipe(
    			map((response: Response) => response)
    		);
	}

	getFirstTopicId(unitId) {
		return this.http.get('/placement/getTopicId/'+unitId)
	        .pipe(
    			map((response: Response) => response)
    		);
	}

	doneUnit(unitId) {
		let request = { unit_id: unitId };
		return this.http.post('/placement/done-unit', request)
	        .pipe(
    			map((response: Response) => response)
    		);
	}

	doneHalfUnit(unitId) {
		let request = { unit_id: unitId };
		return this.http.post('/placement/done-half-unit', request)
	        .pipe(
    			map((response: Response) => response)
    		);
	}

}