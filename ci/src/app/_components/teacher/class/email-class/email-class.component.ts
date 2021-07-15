import {Component, OnInit} from '@angular/core';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import {ClassesManagementService} from '../../../../_services';
import {MatSnackBar} from '@angular/material/snack-bar';
import {ActivatedRoute} from '@angular/router';

@Component({
    selector: 'app-email-class',
    templateUrl: 'email-class.component.html',
    styleUrls: ['email-class.component.scss'],
    providers: [ClassesManagementService]
})
export class TeacherClassEmailComponent implements OnInit {

    public classId: number;
    public class = {
        id: 0,
        name: '',
    };
    public students = [];
    public mail = {
        class_id: 0,
        subject: '',
        body: '',
        students: [],
        for_all_students: true
    };

    public editor = ClassicEditor;

    public backLinkText = 'Back';

    private sub: any;

    constructor(private classService: ClassesManagementService,
                private route: ActivatedRoute,
                public snackBar: MatSnackBar) {
    }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            const classes = this.classService.classes;
            this.class = classes.filter(x => x.id === this.classId)[0];
            this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Email';
            this.classService.getStudents(this.class.id)
                .subscribe(students => {
                    this.students = students;
                });
        });
    }

    onEmail() {
        this.classService.emailClass(this.classId, this.mail).subscribe(res => {
            this.snackBar.open('Email has been successfully sent!', '', {
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
