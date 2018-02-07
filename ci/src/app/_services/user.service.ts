import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions, Response } from '@angular/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/map'

import { AuthenticationService } from './index';
import { User } from '../_models/index';
import { ServerService } from './server.service';

@Injectable()
export class UserService {
    constructor(
        private serverService: ServerService) {
    }

    public getProfile() {
        return this.serverService.get('/profile')
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

       return this.serverService.post('/profile', request)
           .map((res: Response) => { /*console.log(res);*/ })
           .catch(error => {
               console.log(error);
               throw Error(error);
       });
    }

    public changePassword(newPassword: string, confirmedPassword: string) {

        let request = JSON.stringify({
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