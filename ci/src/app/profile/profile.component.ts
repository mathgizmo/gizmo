import { Component, OnInit } from '@angular/core';
import { User } from '../_models/user';
import { UserService } from '../_services/user.service';
import { AuthenticationService } from '../_services/authentication.service';

@Component({
  selector: 'app-profle',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
  providers: [UserService]
})
export class ProfileComponent implements OnInit {
  user: User;
  passwordsMatch: boolean;
  warningMessage: string;

  constructor(
    private userService: UserService,
    private authenticationService: AuthenticationService,
  ) {
    this.user = new User();
    this.passwordsMatch = true;
  }

  ngOnInit() {
    this.userService.getProfile()
      .subscribe(res => {
        //console.log(JSON.stringify(res));
        this.user.username = res['name'];
        this.user.email = res['email'];
        this.user.questionNum = res['question_num'];
        localStorage.setItem('question_num', res['question_num']);
      });
  }

  onChangeProfile() {
    this.userService.changeProfile(this.user)
        .subscribe( res => {
            //console.log('Update Result: ' + res);
            localStorage.setItem('question_num', ""+this.user.questionNum);
        });
  }

  onChangePassword(newPassword: string, confirmedPassword: string){
    if(newPassword != confirmedPassword) {
      this.passwordsMatch = false;
      this.warningMessage = "Password does not match the confirm password!";
      return;
    } else if(newPassword == "") {
      this.passwordsMatch = false;
      this.warningMessage = "You can't use empty passwords!";
      return;
    } else {
      this.passwordsMatch = true;
      this.userService.changePassword(newPassword, confirmedPassword)
        .subscribe(res => {
        //console.log('Change Password Result: ' + res);
        //console.log("Old Token: " + JSON.parse(localStorage.getItem('currentUser')).token);
        this.authenticationService.login(this.user.email, newPassword);
          //.subscribe(() => console.log("New Token: " + JSON.parse(localStorage.getItem('currentUser')).token));
      }, error => {
        // error
      });
    }
  }

}
