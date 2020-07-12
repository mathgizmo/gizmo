import {Component, OnInit} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {Router} from '@angular/router';
import {UserService} from '../../_services/user.service';
import {environment} from '../../../environments/environment';

@Component({
    selector: 'app-to-do',
    templateUrl: './to-do.component.html',
    styleUrls: ['./to-do.component.scss'],
    providers: [UserService]
})
export class ToDoComponent implements OnInit {
    public applications = [];
    public completedApplications = [];
    public selectedAppId = +localStorage.getItem('app_id');
    public showCompletedApplications = false;
    private readonly adminUrl = environment.adminUrl;

    constructor(
        private userService: UserService,
        private sanitizer: DomSanitizer,
        private router: Router
    ) { }

    ngOnInit() {
        this.userService.getToDos()
            .subscribe(response => {
                this.applications = response.filter(app => !app.is_completed);
                this.completedApplications = response.filter(app => app.is_completed);
            });
    }

    onChangeToDo(app) {
        if (!app || app.is_blocked) {
            return;
        }
        localStorage.setItem('app_id', app.id + '');
        this.router.navigate(['/']);
    }

    setIcon(image) {
        if (!image) {
            image = 'images/default-icon.svg';
        }
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

}
