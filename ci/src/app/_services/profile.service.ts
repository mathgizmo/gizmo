import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import { ReplaySubject } from 'rxjs/Rx';
import 'rxjs/add/operator/map'

import { GlobalVariable } from '../../app/globals';
import { Profile } from '../_models/profile';
import { AuthenticationService } from './index';

@Injectable()
export class ProfileService {
	
	private readonly apiUrl = GlobalVariable.BASE_API_URL;
	private readonly CHANGE_PROFILE_URL = this.apiUrl + "/profile";

	constructor(private http: Http, 
		private authenticationService: AuthenticationService) {
  	}

  	public getProfile(): Observable<any> {
      // add authorization header with jwt token
      let headers = new Headers({ 
        'Content-Type': 'application/json', 
        'Authorization': 'Bearer ' + this.authenticationService.token 
      });
      let options = new RequestOptions({ headers: headers });

      return this.http.get(this.CHANGE_PROFILE_URL, options)
      	.map(res => { return res.json();
		})
      	.catch(error => {
          throw Error(error);
      	});
  	}

	public changeProfile(user: Profile) {
	  // add authorization header with jwt token
    let headers = new Headers({ 
      'Content-Type': 'application/json', 
      'Authorization': 'Bearer ' + this.authenticationService.token 
    });
    let options = new RequestOptions({ headers: headers });

    let request = JSON.stringify({
      name: user.userName,
      email: user.email,
      question_num: parseInt(String(user.questionNum), 10)
    });

    return this.http.post(this.CHANGE_PROFILE_URL, request, options)
    	.map((res: Response) => { /*console.log(res);*/ })
      .catch(error => {
    		console.log(error);
    		throw Error(error);
   	 });
	}

  public changePassword(oldPassword: string, 
    newPassword: string, confirmedPassword: string) {
    
    // add authorization header with jwt token
    let headers = new Headers({ 
      'Content-Type': 'application/json', 
      'Authorization': 'Bearer ' + this.authenticationService.token 
    });
    let options = new RequestOptions({ headers: headers });

    let request = JSON.stringify({
      old_password: oldPassword,
      password: newPassword, 
      confirm_password: confirmedPassword
    });

    return this.http.post(this.CHANGE_PROFILE_URL, request, options)
      .map((res: Response) => { console.log(res); })
      .catch(error => {
        console.log(error);
        throw Error(error);
      });
  }

}