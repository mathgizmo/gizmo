import { Component, OnInit } from '@angular/core';
import { AuthenticationService } from '../../../_services/index';
import { Router } from '@angular/router';

@Component({
  selector: 'app-try',
  template: '<div>Try without registration</div>',
  host: { '(click)': 'onClick()'}
})

export class TryComponent implements OnInit {

  constructor(
  	private authenticationService: AuthenticationService,
  	private router: Router,
  	) { 
  }

  ngOnInit() {
    // reset login status
    this.authenticationService.logout();
  }

  private onClick() {
    
    /* https://stackoverflow.com/questions/42538280/angular2-how-to-use-on-the-frontend-crypto-pbkdf2sync-function-from-node-js
    let crypto;
    try {
      crypto = require('crypto');
    } catch (err) {
      console.log('crypto support is disabled!');
    }
  	let id = crypto.randomBytes(20, (err, buf) => {
  	  if (err) throw err;
  	}).toString('hex');
    */

    let id = this.randomString();

  	let email = id+'@somemail.com';
  	let password = id;
  	let username = id;

    this.authenticationService.register(username, email, password)
        .subscribe(res => {
            if (res['success'] === true) {
               this.authenticationService.login(email, password)
                 .subscribe(res => {
                    if (res == true) {
                        this.router.navigate(['/']);
                    }
               });
            }
        });
  }

  private randomString() {
    let length = 50; // max 64
    let id = "";
    let alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for(let i = 0; i < length; i++) {
        id += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
    }
    return id;
  }

}
