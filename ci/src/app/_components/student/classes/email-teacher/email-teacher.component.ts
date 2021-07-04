import {Component, OnInit} from '@angular/core';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import {ClassesManagementService, UserService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import {MatSnackBar} from '@angular/material/snack-bar';

@Component({
    selector: 'app-email-teacher',
    templateUrl: 'email-teacher.component.html',
    styleUrls: ['email-teacher.component.scss'],
    providers: [UserService, ClassesManagementService]
})
export class StudentEmailTeacherComponent implements OnInit {

    public classId: number;

    public myClass = {
        id: 0,
        name: '',
        teacher_email: ''
    };
    public mail = {
        subject: '',
        body: '',
    };

    public editor = ClassicEditor;

    public backLinkText = 'Back';

    public myClasses = [];
    private sub: any;

    constructor(private userService: UserService,
            private classService: ClassesManagementService,
            private route: ActivatedRoute,
            public snackBar: MatSnackBar) {
    }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            this.userService.getClasses()
                .subscribe(response => {
                    this.myClasses = response['my_classes'];
                    this.myClass = this.myClasses.find(obj => {
                        return obj.id === this.classId;
                    });
                    this.backLinkText = 'My Classes > ' + (this.myClass ? this.myClass.name : this.classId) + ' > Email';
                });
        });
    }

    onEmail() {
        this.classService.emailClass(this.classId, this.mail).subscribe(res => {
            this.snackBar.open('Email have been successfully sent!', '', {
                duration: 3000,
                panelClass: ['success-snackbar']
            });
        }, error => {
            let message = '';
            if (typeof error === 'object') {
                Object.values(error).forEach(x => {
                    message += x + ' ';
                });
            } else {
                message = error;
            }
            this.snackBar.open(message ? message : 'Error occurred while sending email!', '', {
                duration: 3000,
                panelClass: ['error-snackbar']
            });
        });
    }
}
