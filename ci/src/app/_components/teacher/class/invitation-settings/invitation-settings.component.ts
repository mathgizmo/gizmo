import {Component, Inject, OnInit} from '@angular/core';
import {ClassesManagementService} from '../../../../_services';
import {ActivatedRoute} from '@angular/router';
import {APP_BASE_HREF} from '@angular/common';
import { Clipboard } from '@angular/cdk/clipboard';

@Component({
    selector: 'app-class-invitation-settings',
    templateUrl: './invitation-settings.component.html',
    styleUrls: ['./invitation-settings.component.scss']
})
export class ClassInvitationSettingsComponent implements OnInit {

    public classId: number;
    public class = {
        id: 0,
        key: null,
        name: '',
        class_type: 'other',
        subscription_type: 'open',
        invitations: ''
    };

    public classJoinURL = '';
    public tutorialURL = '';

    public backLinkText = 'Back';
    private sub: any;

    constructor(
        @Inject(APP_BASE_HREF) public baseHref: string,
        public clipboard: Clipboard,
        private route: ActivatedRoute,
        private classService: ClassesManagementService) {
    }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.classId = +params['class_id'];
            const classes = this.classService.classes;
            this.class = classes.filter(x => x.id === this.classId)[0];
            this.backLinkText = 'Classrooms > ' + (this.class ? this.class.name : this.classId) + ' > Invitation Settings';
            this.classJoinURL = window.location.origin + this.baseHref + 'classroom/' + this.class.key + '/join';
            this.tutorialURL = window.location.origin + this.baseHref + 'tutorial';
        });
    }
}
