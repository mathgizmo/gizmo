import { Component, OnInit } from '@angular/core';
import {Router, ActivatedRoute, Params} from '@angular/router';

@Component({
  selector: 'app-question-preview',
  templateUrl: './question-preview.component.html',
  styleUrls: ['./question-preview.component.scss']
})
export class QuestionPreviewComponent implements OnInit {
    question: any = null;

    constructor(private activatedRoute: ActivatedRoute) {
    }

    ngOnInit() {
        this.activatedRoute.queryParams.subscribe((params: Params) => {
            this.question = {
                reply_mode: params['reply_mode'],
                question: params['question'],
                answers: []
            };
            let i = 1;
            while (params['answer' + i]) {
                this.question.answers.push(
                    {
                        value: params['answer' + i]
                    }
                );
                i++;
            }
            try {
                document.getElementById('continue-button').style.display = 'none';
                document.getElementById('main-menu').style.display = 'none';
            } catch (ex) {
            }
        });
    }

}
