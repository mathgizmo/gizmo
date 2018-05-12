import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { UserService } from '../../../../_services/user.service';
import { User } from '../../../../_models/user';

@Component({
    selector: 'no-dialog',
    templateUrl: 'no-dialog.component.html',
    styleUrls: ['no-dialog.component.scss'],
    providers: [UserService]
})
export class NoDialogComponent {
    user: User = new User();

    constructor(
        private userService: UserService,
        public dialogRef: MatDialogRef<NoDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        this.user.questionNum = +localStorage.getItem('question_num') || 5;
    }

    onClick(): void {
        this.userService.changeProfile(this.user)
        .subscribe( res => {
          localStorage.setItem('question_num', ""+this.user.questionNum);
        });
        this.dialogRef.close();
    }
}