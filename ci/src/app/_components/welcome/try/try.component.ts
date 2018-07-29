import { Component, OnInit } from '@angular/core';
import { AuthenticationService } from '../../../_services/index';
import { Router } from '@angular/router';

@Component({
  selector: 'app-try',
  template: '<button mat-button class="button try-button">Try without registration</button>',
  host: { '(click)': 'onTry()'},
  styles: [`
    .try-button {
      width: 100%;
      height: 100%;
      padding: 22px;
      margin: 0;
      background-color: #FFB133 !important;
    }
  `]
})

export class TryComponent implements OnInit {

  constructor(
  	private authenticationService: AuthenticationService,
  	private router: Router
  	) { 
  }

  ngOnInit() {
    // reset login status
    this.authenticationService.logout();
  }

  public onTry():any {
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
                        this.router.navigate(['placement']);
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
