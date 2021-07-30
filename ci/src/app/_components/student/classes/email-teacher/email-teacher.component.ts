import {Component, OnInit} from '@angular/core';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import {ClassesManagementService, UserService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import {MatSnackBar} from '@angular/material/snack-bar';

@Component({
    selector: 'app-email-teacher',
    templateUrl: 'email-teacher.component.html',
    styleUrls: ['email-teacher.component.scss']
})
export class StudentEmailTeacherComponent implements OnInit {

    public classId: number;

    public myClass = {
        id: 0,
        name: '',
        teacher_email: '',
        is_researchable: 0
    };
    public mail = {
        subject: '',
        body: '',
        teachers: [],
        for_all_teachers: true
    };
    public message: string;
    public teachers = [];

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
            this.classService.getTeachers(this.classId, {
                receive_emails_from_students: true
            }).subscribe(res => {
                this.teachers = res['teachers'];
            });
            this.userService.getClass(this.classId)
                .subscribe(response => {
                    this.myClass = response;
                    this.backLinkText = 'My Classes > ' + (this.myClass ? this.myClass.name : this.classId) + ' > Email';
                });
        });
    }

    onEmail() {
        this.classService.emailClass(this.classId, this.mail).subscribe(res => {
            this.snackBar.open('Email has been successfully sent!', '', {
                duration: 3000,
                panelClass: ['success-snackbar']
            });
            this.mail = {
                subject: '',
                body: '',
                teachers: [],
                for_all_teachers: true
            };
            this.message = 'Email has been successfully sent!';
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
