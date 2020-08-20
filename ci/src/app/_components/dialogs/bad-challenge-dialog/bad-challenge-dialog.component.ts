import {Component, OnInit, OnDestroy, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

import {BaseDialogComponent} from '../base-dialog.component';

@Component({
    selector: 'bad-challenge-dialog',
    templateUrl: 'bad-challenge-dialog.component.html',
    styleUrls: ['bad-challenge-dialog.component.scss']
})
export class BadChallengeDialogComponent extends BaseDialogComponent<BadChallengeDialogComponent> {
    text: string;
    topic_id: number;
    lesson_id: number;

    constructor(
        public dialogRef: MatDialogRef<BadChallengeDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        this.text = data.data;
        this.topic_id = data.topic_id;
        this.lesson_id = data.lesson_id;
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '80vw' : '40vw';
        const height = (this.orientation === 'portrait') ? '30vh' : '32.5vh';
        this.updateDialogSize(width, height);
    }

}
