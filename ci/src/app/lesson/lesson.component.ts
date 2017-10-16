import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { TopicService } from '../_services/index';

@Component({
    moduleId: module.id,
    templateUrl: 'lesson.component.html',
    providers: [TopicService]
})

export class LessonComponent implements OnInit {
    lessonTree: any = [];
    topic_id: number;
    lesson_id: number;
    question: any = null;
    answer: string = '';
    private sub: any;

    constructor(
            private topicService: TopicService,
            private route: ActivatedRoute
            ) { }

    ngOnInit() {
        this.sub = this.route.params.subscribe(params => {
            this.topic_id = +params['topic_id']; // (+) converts string 'id' to a number
            this.lesson_id = +params['lesson_id']; // (+) converts string 'id' to a number

            // In a real app: dispatch action to load the details here.
            // get lesson tree from API
            this.topicService.getLesson(this.topic_id, this.lesson_id)
                .subscribe(lessonTree => {
                    this.lessonTree = lessonTree;
                    console.log(lessonTree);
                    if (lessonTree['questions'].length) {
                        this.setUpQuestion(lessonTree['questions'][0]);
                    }
                });
         });

    }

    setUpQuestion(question) {
        if (['mcq3', 'mcq4', 'mcq6'].indexOf(question.reply_mode) >= 0) {
            question.answer_mode = 'radio';
        } else {
            question.answer_mode = 'input';
        }
        this.question = question;
    }
    
    checkAnswer() {
        if (this.isCorrect(this.question, this.answer)) {
            console.log('good');
        } else {
            console.log('bad');
        }
    }
    
    isCorrect(question, answer) {
        if (answer == '') return false;
        if (question.answer_mode == 'radio') {
            answer = +answer;
            if (answer < 0 || answer >= question.answers.length) return false;
            if (question.answers[answer].is_correct) {
                return true;
            }
        } else {
            if (question.answers[0].value = answer) {
                return true;
            }
        }
        return false;
    }

}