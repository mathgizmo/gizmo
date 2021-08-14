import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';
import {UserService, SettingsService} from '../../../../_services';

@Component({
    selector: 'app-research-consent-dialog',
    templateUrl: 'research-consent-dialog.component.html',
    styleUrls: ['research-consent-dialog.component.scss'],
    providers: [SettingsService]
})
export class ResearchConsentDialogComponent extends BaseDialogComponent<ResearchConsentDialogComponent> implements OnInit {
    public classId = null;
    public consent = {
        is_consent_read: 0,
        is_element1_accepted: 0,
        is_element2_accepted: 0,
        is_element3_accepted: 0,
        is_element4_accepted: 0
    };
    public allSelected: boolean = null;
    public noneSelected: boolean = null;
    public manualSelect: boolean = null;
    public consentMessage = '';

    constructor(
        private userService: UserService,
        private settingsService: SettingsService,
        public dialogRef: MatDialogRef<ResearchConsentDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.class_id) {
            this.classId = this.data.class_id;
        }
        if (this.data.consent) {
            this.consent = this.data.consent;
        }
        this.resizeDialog();
        this.settingsService.getSetting('research_consent')
            .subscribe(result => {
                if (result && result.value) {
                    this.consentMessage = result.value;
                }
            });
    }

    onSubmit() {
        this.userService.updateClassConsent(this.classId, this.consent).subscribe(res => { });
        this.dialogRef.close(this.consent);
    }

    checkSelected() {
        this.allSelected = Boolean(this.consent.is_element1_accepted && this.consent.is_element2_accepted
            && this.consent.is_element3_accepted && this.consent.is_element4_accepted);
        this.noneSelected = Boolean(!this.consent.is_element1_accepted && !this.consent.is_element2_accepted
            && !this.consent.is_element3_accepted && !this.consent.is_element4_accepted);
    }

    setSelected(selected: boolean) {
        this.allSelected = selected;
        this.noneSelected = !selected;
        this.consent.is_element1_accepted = this.consent.is_element2_accepted
            = this.consent.is_element3_accepted = this.consent.is_element4_accepted = (selected ? 1 : 0);
    }


    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '60vw';
        this.dialogRef.updateSize(width);
    }

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        /* if (event.key === 'Enter') {
            this.onSubmit();
        } */
    }

}
