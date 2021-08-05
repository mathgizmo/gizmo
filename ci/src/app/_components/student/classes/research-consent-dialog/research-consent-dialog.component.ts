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
    public consentMessage = `The developers of the <strong>HNP webapp</strong> with support from the <a href="https://heqco.ca/" target="_blank"><strong>Higher Education Quality Council of Ontario</strong></a> along with <a href="https://www.georgebrown.ca/" target="_blank"><strong>George Brown College</strong></a> and <a href="https://www.mcmaster.ca/" target="_blank"><strong>McMaster university</strong></a> are inviting you to help evaluate it as a useful educational tool for health numeracy.
        <br/>
        Your teacher has integrated the HNP webapp into your course materials and you will have access to the application whether you participate or not, thus participation is voluntary. Your teacher will not know whether you will participate or not, and we aim to evaluate the application not the research participants.
        <br/>
        We would like to invite you to take part in one or more of 4 formats in which we will be using to evaluate the app. They are described at length in the letter of informed consent that you need to read. You can have access to it here. Three of them involve compensation to offset time and any costs you incur, and one does not require you to do anything except provide access to data. lease read carefully
        <br/>
        To get more information about the study please email <strong>Taras Gula</strong> at <a href="mailto:tgula@georgebrown.ca">tgula@georgebrown.ca</a> or <strong>Miroslav Lovric</strong> at <a href="mailto:lovric@mcmaster.ca">lovric@mcmaster.ca</a>.`;

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
