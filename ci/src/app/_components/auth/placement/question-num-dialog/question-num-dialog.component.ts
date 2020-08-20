/*import {Component, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';
import {UserService} from '../../../../_services/user.service';
import {User} from '../../../../_models/user';

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
        this.user.question_num = +localStorage.getItem('question_num') || 3;
    }

    onClick(): void {
        this.userService.changeProfile(this.user)
            .subscribe(res => {
                localStorage.setItem('question_num', '' + this.user.question_num);
            });
        this.dialogRef.close();
    }
}
*/
