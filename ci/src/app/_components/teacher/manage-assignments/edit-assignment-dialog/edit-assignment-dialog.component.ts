import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';

@Component({
    selector: 'edit-assignment-dialog',
    templateUrl: 'edit-assignment-dialog.component.html',
    styleUrls: ['edit-assignment-dialog.component.scss'],
})
export class EditAssignmentDialogComponent extends BaseDialogComponent<EditAssignmentDialogComponent> {

    assignment = {
        'name': '',
        'icon': null,
        'tree': null,
        'allow_any_order': false,
        'testout_attempts': -1
    };
    tree = [];
    title = 'Edit Assignment';
    public icons = [];

    public showImages = false;

    private readonly adminUrl = environment.adminUrl;

    constructor(
        private sanitizer: DomSanitizer,
        public dialogRef: MatDialogRef<EditAssignmentDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
        if (data.assignment) {
            // tslint:disable-next-line:indent
        	this.assignment = data.assignment;
        }
        if (data.title) {
            // tslint:disable-next-line:indent
        	this.title = data.title;
        }
        if (data.tree) {
            // tslint:disable-next-line:indent
            this.tree = data.tree;
        }
        if (data.icons) {
            // tslint:disable-next-line:indent
            this.icons = data.icons;
        }
    }

    onSave() {
        this.assignment.tree = $('#tree-form').serialize();
        this.dialogRef.close(this.assignment);
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
        this.assignment.icon = icon;
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
    }

}
