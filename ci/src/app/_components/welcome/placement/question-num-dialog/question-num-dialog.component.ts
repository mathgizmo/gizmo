import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { UserService } from '../../../../_services/user.service';
import { User } from '../../../../_models/user';

@Component({
    selector: 'question-num-dialog',
    templateUrl: 'question-num-dialog.component.html',
    styleUrls: ['question-num-dialog.component.scss'],
    providers: [UserService]
})
export class QuestionNumDialogComponent {
    user: User = new User();

    constructor(
        private userService: UserService,
        public dialogRef: MatDialogRef<QuestionNumDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        this.user.questionNum = +localStorage.getItem('question_num') || 3;
    }

    onClick(): void {
        this.userService.changeProfile(this.user)
        .subscribe( res => {
          localStorage.setItem('question_num', ""+this.user.questionNum);
        });
        this.dialogRef.close();
    }
}