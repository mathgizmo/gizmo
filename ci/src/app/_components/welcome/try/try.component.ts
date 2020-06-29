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
      margin: 0;
      background-color: #11A02E !important;
      font-size: 18px !important;
    }
    @media screen and (max-width: 1200px) {
        .try-button {
            font-size: 14px !important;
        }
    }
    @media screen and (max-width: 1024px) {
        .try-button {
        font-size: 12px !important;
      }
    }
    @media screen and (max-width: 768px) {
      .try-button {
        font-size: 8px !important;
      }
    }
  `]
})

export class TryComponent implements OnInit {

  constructor(private authenticationService: AuthenticationService, private router: Router) {}

  ngOnInit() {
    this.authenticationService.logout();
  }

  public onTry(): any {
    const id = this.randomString();
    const email = id + '@somemail.com';
    const password = id;
    const username = id;
    this.authenticationService.register(username, email, password)
        .subscribe(res => {
            if (res['success'] === true) {
               this.authenticationService.login(email, password)
                 .subscribe(result => {
                    if (result === true) {
                        this.router.navigate(['']);
                    }
               });
            }
        });
  }

  private randomString() {
    const length = 50; // max 64
    let id = '';
    const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (let i = 0; i < length; i++) {
        id += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
    }
    return id;
  }

}
