import { Component, OnInit } from '@angular/core';
import { Profile } from '../_models/profile';
import { ProfileService } from '../_services/profile.service';
import { AuthenticationService } from '../_services/authentication.service';


@Component({
  selector: 'app-profle',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
  providers: [ProfileService]
})
export class ProfileComponent implements OnInit {
  user: Profile;
  passwordsMatch: boolean;
  warningMessage: string;

  constructor(
    private profileService: ProfileService,
    private authenticationService: AuthenticationService
  ) {
    this.user = new Profile();
    this.passwordsMatch = true;
    this.profileService.getProfile()
        .subscribe(res => {
            this.user.userName = res.message.name;
            this.user.email = res.message.email;
            this.user.questionNum = res.message.question_num;
            /*console.log(res.message);*/
        });
  }

  ngOnInit() {
  }

  onChangeProfile() {
    this.profileService.changeProfile(this.user)
        .subscribe( res => {
            //console.log('Update Result: ' + res);
        });
  }

  onChangePassword(oldPassword: string, 
    newPassword: string, confirmedPassword: string){
    if(newPassword != confirmedPassword) {
      this.passwordsMatch = false;
      this.warningMessage = "Password does not match the confirm password!";
      return;
    } else if(newPassword == "" || oldPassword == "") {
      this.passwordsMatch = false;
      this.warningMessage = "You can't use empty passwords!";
      return;
    } else {
      this.passwordsMatch = true;
      this.profileService.changePassword(oldPassword, 
      newPassword, confirmedPassword)
        .subscribe(res => {
        //console.log('Change Password Result: ' + res);
        this.authenticationService.logout();
        
      }, error => {
        // error
      });
    }
  }

}
