import {Component, OnInit} from '@angular/core';
import {User} from '../../_models/user';
import {AuthenticationService, CountryService, UserService} from '../../_services/index';
import { TagService, Tag } from '../../_services/tag.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import {forkJoin, of, timer} from 'rxjs';
import {catchError, finalize, takeWhile, tap} from 'rxjs/operators';

@Component({
    selector: 'app-profile',
    templateUrl: './profile.component.html',
    styleUrls: ['./profile.component.scss']
})
export class ProfileComponent implements OnInit {
    public user: User;
    public newPassword = '';
    public confirmedPassword = '';
    public oldEmail = null;
    public showOldEmail = false;
    public linkTimer = 0;
    public passwordsMatch = true;
    public badEmail = false;
    public warningMessage: string;
    public countries = [];
    public selectedCountry = {
        id: 1,
        title: 'Canada',
        code: 'CA'
    };
    public isResearcher = false;
    public allTags: Tag[] = [];
    public selectedTagIds: number[] = [];
    public tagsLoading = false;
    public isLoading = true; // block UI interactions until initial data loads

    constructor(
        private userService: UserService,
        private authenticationService: AuthenticationService,
        private countryService: CountryService,
        public snackBar: MatSnackBar,
        private tagService: TagService
    ) {
        this.user = new User();
    }

    ngOnInit() {
        // Load countries, profile, and tags in parallel; keep spinner until all complete
        const countries$ = this.countryService.getCountries().pipe(
            tap(countries => { this.countries = countries || []; }),
            catchError(_ => of([]))
        );
        const profile$ = this.userService.getProfile().pipe(
            tap(res => {
                const user = res && res['user'] ? res['user'] : null;
                const tags = (res && res['tags']) || [];
                this.selectedTagIds = tags.map(t => t.id);
                if (user) {
                    localStorage.setItem('app_id', user.app_id);
                    this.user = user;
                    this.oldEmail = this.user.email;
                    this.user.email = user.email_new ? user.email_new : user.email;
                    this.showOldEmail = this.oldEmail !== this.user.email;
                    const userCountry = this.countries.filter(x => x.id === this.user.country_id);
                    if (userCountry.length > 0) {
                        this.selectedCountry = userCountry[0];
                    } else if (this.countries.length > 0) {
                        const ca = this.countries.filter(x => x.code === 'CA')[0];
                        this.selectedCountry = ca || this.countries[0];
                    }
                    this.isResearcher = user.role === 'researcher';
                }
            }),
            catchError(_ => of(null))
        );
        const tags$ = this.tagService.getTags().pipe(
            tap(tags => { this.allTags = tags || []; }),
            catchError(_ => of([]))
        );

        forkJoin([countries$, profile$, tags$])
            .pipe(finalize(() => { this.isLoading = false; }))
            .subscribe(_ => {}, _ => { this.isLoading = false; });
    }

    loadTags() {
        this.tagsLoading = true;
        this.tagService.getTags().subscribe(tags => {
            this.allTags = tags;
            this.tagsLoading = false;
        }, _ => this.tagsLoading = false);
    }

    toggleTag(tagId: number) {
        const idx = this.selectedTagIds.indexOf(tagId);
        if (idx >= 0) { this.selectedTagIds.splice(idx, 1); } else { this.selectedTagIds.push(tagId); }
    }

    onChangeProfile() {
        this.user.country_id = this.selectedCountry.id;
        if (this.user.role === 'researcher' || this.user.role === 'teacher') {
            this.user.role = this.isResearcher ? 'researcher' : 'teacher';
        }
        this.authenticationService.saveUserValue(this.user);
        this.userService.changeProfile(this.user, this.selectedTagIds)
            .subscribe(res => {
                this.passwordsMatch = true;
                if (Array.isArray(res['email'])) {
                    this.warningMessage = res['email'][0];
                    this.badEmail = true;
                } else {
                    this.badEmail = false;
                }
                this.showOldEmail = this.oldEmail !== this.user.email;
                this.snackBar.open('Profile info has been successfully updated!', '', {
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
                this.snackBar.open(message ? message : 'Error occurred while changing profile info!', '', {
                    duration: 3000,
                    panelClass: ['error-snackbar']
                });
            });
    }

    onChangePassword() {
        this.badEmail = false;
        if (this.newPassword !== this.confirmedPassword) {
            this.passwordsMatch = false;
            this.warningMessage = 'Password does not match the confirm password!';
            return;
        } else if (this.newPassword === '') {
            this.passwordsMatch = false;
            this.warningMessage = 'You can\'t use empty passwords!';
            return;
        } else {
            this.passwordsMatch = true;
            this.userService.changePassword(this.newPassword, this.confirmedPassword)
                .subscribe(res => {
                    this.authenticationService.login(this.user.email, this.newPassword);
                    this.snackBar.open('Password has been successfully updated!', '', {
                        duration: 3000,
                        panelClass: ['success-snackbar']
                    });
                }, error => {
                    this.snackBar.open(error.password ? error.password : 'Error occurred while changing password!', '', {
                        duration: 3000,
                        panelClass: ['error-snackbar']
                    });
                });
        }
    }

    onVerifyEmail() {
        if (this.user.email) {
            this.authenticationService.sendEmailVerificationLink(this.user.email)
                .subscribe(res => {
                    this.linkTimer = 60;
                    this.initTimer();
                    this.snackBar.open('We sent email verification link to your email address!', '', {
                        duration: 3000,
                        panelClass: ['success-snackbar']
                    });
                }, error => {
                    this.linkTimer = 60;
                    this.initTimer();
                    this.snackBar.open(error, '', {
                        duration: 3000,
                        panelClass: ['error-snackbar']
                    });
                });
        }
    }

    initTimer() {
        timer(1000, 1000)
            .pipe(
                takeWhile( () => this.linkTimer > 0 ),
                tap(() => this.linkTimer--)
            )
            .subscribe( () => {});
    }

}
