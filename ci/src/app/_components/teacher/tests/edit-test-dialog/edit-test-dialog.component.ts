import {Component, HostListener, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';
import {TestService} from '../../../../_services';
@Component({
    selector: 'app-edit-test-dialog',
    templateUrl: 'edit-test-dialog.component.html',
    styleUrls: ['edit-test-dialog.component.scss'],
    providers: [TestService]
})
export class EditTestDialogComponent extends BaseDialogComponent<EditTestDialogComponent> implements OnInit {

    public test = {
        'name': '',
        'icon': null,
        'tree': null,
        'allow_any_order': 0,
        'allow_back_tracking': 0,
        'duration': 0,
        'question_num': 1,
        'total_questions_count': 0
    };
    public questionsCount = 0;
    public tree = [];
    public title = 'Edit Test';
    public icons = [];

    public showImages = false;

    private readonly adminUrl = environment.adminUrl;

    constructor(
        private testService: TestService,
        private sanitizer: DomSanitizer,
        public dialogRef: MatDialogRef<EditTestDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.test) {
            this.test = this.data.test;
            this.questionsCount = this.test.total_questions_count || 0;
        }
        if (this.data.title) {
            this.title = this.data.title;
        }
        if (this.data.tree) {
            this.tree = this.data.tree;
        }
        if (this.data.icons) {
            this.icons = this.data.icons;
        }
        this.resizeDialog();
    }

    onSave() {
        this.test.tree = $('#tree-form').serialize();
        this.dialogRef.close(this.test);
    }

    getQuestionsCount() {
        const tree = $('#tree-form').serialize();
        this.testService.getQuestionsCount(tree, this.test.question_num).subscribe(questions_count => {
            this.questionsCount = questions_count;
        });
    }

    hasCheckedChildrenLevel(level) {
        return (level.children.filter( (unit) => {
            return unit.checked || (unit.children.filter( (topic) => {
                return topic.checked || (topic.children.filter( (lesson) => {
                    return lesson.checked;
                })).length !== 0;
            })).length !== 0;
        })).length === 0;
    }

    hasCheckedChildrenUnit(unit) {
        return (unit.children.filter( (topic) => {
            return topic.checked || (topic.children.filter( (lesson) => {
                return lesson.checked;
            })).length !== 0;
        })).length === 0;
    }

    hasCheckedChildrenTopic(topic) {
        return (topic.children.filter( (lesson) => {
            return lesson.checked;
        })).length === 0;
    }

    onImageSelected(icon) {
        this.test.icon = icon;
        this.showImages = !this.showImages;
    }

    resizeDialog() {
        const width = (this.orientation === 'portrait') ? '96vw' : '60vw';
        this.dialogRef.updateSize(width);
    }

    setIcon(image) {
        if (!image) {
            image = 'images/default-icon.svg';
        }
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

    onExpandTree(item) {
        $(item).next().toggleClass('collapse');
        const iconElem = item.parentElement.querySelector('.expand-icon');
        if (iconElem.classList.contains('fa-plus')) {
            iconElem.classList.remove('fa-plus');
            iconElem.classList.add('fa-minus');
        } else {
            iconElem.classList.remove('fa-minus');
            iconElem.classList.add('fa-plus');
        }
    }

    onTreeElementChecked(item) {
        const checked = $(item).prop('checked');
        const container = $(item).parent();
        container.find('input[type="checkbox"]').prop({
            indeterminate: false,
            checked: checked
        });
        function checkSiblings(el) {
            const parent = el.parent().parent();
            let all = true;
            el.siblings().each(function () {
                return all = ($(item).children('input[type="checkbox"]').prop('checked') === checked);
            });
            if (all && checked) {
                parent.children('input[type="checkbox"]').prop({
                    indeterminate: false,
                    checked: checked
                });
                checkSiblings(parent);
            } else if (all && !checked) {
                parent.children('input[type="checkbox"]').prop('checked', checked);
                parent.children('input[type="checkbox"]').prop('indeterminate', (parent.find('input[type="checkbox"]:checked').length > 0));
                checkSiblings(parent);
            } else {
                el.parents('li').children('input[type="checkbox"]').prop({
                    indeterminate: true,
                    checked: false
                });
            }
        }
        checkSiblings(container);
        this.getQuestionsCount();
    }

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        /* if (event.key === 'Enter') {
            this.dialogRef.close();
        } */
    }

}
