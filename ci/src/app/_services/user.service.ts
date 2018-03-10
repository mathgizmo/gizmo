import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map'

import { User } from '../_models/index';

import { AuthenticationService } from './index';
import { HttpService } from './http.service';

@Injectable()
export class UserService {
    
    constructor(
        private http: HttpService) {
    }

    public getProfile() {
      return this.http.get('/profile')
        .map((res:Response) => res)
        .catch(error => {
          throw Error(error);
      });
    }

    public changeProfile(user: User) {
      let request = JSON.stringify({
          name: user.username,
          email: user.email,
          question_num: user.questionNum
      });

      return this.http.post('/profile', request)
          .map((res: Response) => { })
          .catch(error => {
              console.log(error);
              throw Error(error);
      });
    }

    public changePassword(newPassword: string, 
      confirmedPassword: string) {

      let request = JSON.stringify({
          password: newPassword, 
          confirm_password: confirmedPassword
      });

      return this.http.post('/profile', request)
        .map((res: Response) => { })
        .catch(error => {
          console.log(error);
          throw Error(error);
        });
    }
}