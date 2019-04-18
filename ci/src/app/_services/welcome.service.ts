import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

import { HttpService } from './http.service';

@Injectable()
export class WelcomeService {

	constructor(private http: HttpService) { }

	getWelcomeTexts() {
	    return this.http.get('/welcome');
	}

}