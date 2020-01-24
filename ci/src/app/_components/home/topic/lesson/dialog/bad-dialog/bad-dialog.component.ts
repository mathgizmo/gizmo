import {Component, OnInit, OnDestroy, Inject} from '@angular/core';
import {MatDialogRef, MAT_DIALOG_DATA} from '@angular/material';

import {BaseDialogComponent} from '../base-dialog.component';

@Component({
    selector: 'bad-dialog',
    templateUrl: 'bad-dialog.component.html',
    styleUrls: ['bad-dialog.component.scss']
})
export class BadDialogComponent extends BaseDialogComponent<BadDialogComponent> {
    answers: string[];
    explanation: string;
    showAnswer: boolean;
    showExplanation = false;
    scrollButtonIsPressed = false;
    showExplanationScrollButtons = false;

    constructor(
        public dialogRef: MatDialogRef<BadDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        this.answers = data.data;
        this.explanation = data.explanation;
        this.showAnswer = data.showAnswers;
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '80vw' : '35vw';
        const height = (this.orientation === 'portrait') ? '30vh' : '32.5vh';
        this.updateDialogSize(width, height);
    }

    showExplanationOnClick() {
        this.showExplanation = !this.showExplanation;
        setTimeout(() => {
            MathJax.Hub.Queue(['Typeset', MathJax.Hub]);
            this.showExplanationScrollButtons = document.getElementById('dialog-content').clientHeight
                < document.getElementById('explanation').clientHeight;
        }, 100);
    }

    scrollExplanation(direction = 'down') {
        const dialog = document.getElementById('dialog-content');
        if (direction === 'up') {
            dialog.scrollTop -= 5;
        } else {
            dialog.scrollTop += 5;
        }
        if (this.scrollButtonIsPressed) {
            setTimeout( () => {
                this.scrollExplanation(direction);
            }, 50);
        }
    }

    startScrollingExplanation(direction = 'down') {
        this.scrollButtonIsPressed = true;
        this.scrollExplanation(direction);
    }

    stopScrollingExplanation(direction = 'down') {
        this.scrollButtonIsPressed = false;
    }

}
