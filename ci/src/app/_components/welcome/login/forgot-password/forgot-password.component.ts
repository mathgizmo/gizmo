import { Component, OnInit } from '@angular/core';
import { AuthenticationService } from '../../../../_services/index';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.scss']
})
export class ForgotPasswordComponent implements OnInit {
  email: string;
  message: boolean = false;

  constructor(private authenticationService: AuthenticationService) { }

  ngOnInit() {
  }

  sendEmail() {
  	this.authenticationService.sendPasswordResetEmail(this.email)
        .subscribe(result => {
          // 
        });
    this.message = true;
  }

}
