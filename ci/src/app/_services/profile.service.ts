import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import { ReplaySubject } from 'rxjs/Rx';
import 'rxjs/add/operator/map'

import { Profile } from '../_models/profile';
import { ServerService } from './server.service';

@Injectable()
export class ProfileService {

	constructor(private http: Http, 
    private serverService: ServerService) {
  }

 	public getProfile() {
     return this.serverService.get('/profile')
     	.map((res:Response) => res)
     	.catch(error => {
         throw Error(error);
     	});
 	}

	public changeProfile(user: Profile) {
    let request = JSON.stringify({
      name: user.userName,
      email: user.email,
      question_num: parseInt(String(user.questionNum), 10)
    });

    return this.serverService.post('/profile', request)
    	.map((res: Response) => { /*console.log(res);*/ })
      .catch(error => {
    		console.log(error);
    		throw Error(error);
   	 });
	}

  public changePassword(oldPassword: string, 
    newPassword: string, confirmedPassword: string) {

    let request = JSON.stringify({
      old_password: oldPassword,
      password: newPassword, 
      confirm_password: confirmedPassword
    });

    return this.serverService.post('/profile', request)
      .map((res: Response) => { 
        //console.log(res); 
      })
      .catch(error => {
        console.log(error);
        throw Error(error);
      });
  }

}