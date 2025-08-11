import {Component, HostListener, Inject, OnInit, AfterViewInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';

import {BaseDialogComponent} from '../../../dialogs/base-dialog.component';
import {environment} from '../../../../../environments/environment';
import {DomSanitizer} from '@angular/platform-browser';

@Component({
    selector: 'app-edit-assignment-dialog',
    templateUrl: 'edit-assignment-dialog.component.html',
    styleUrls: ['edit-assignment-dialog.component.scss'],
})
export class EditAssignmentDialogComponent extends BaseDialogComponent<EditAssignmentDialogComponent> implements OnInit, AfterViewInit {

    assignment = {
        'name': '',
        'icon': null,
        'tree': null,
        'allow_any_order': false,
        'testout_attempts': 0,
        'question_num': 3,
        'tag_id': null
    };
    tree = [];
    title = 'Edit Assignment';
    public icons = [];

    public showImages = false;

    private readonly adminUrl = environment.adminUrl;

    public teacherTags: any[] = [];

    public selectedTagId: number = null;
    private originalTree: any[] = [];

    constructor(
        private sanitizer: DomSanitizer,
        public dialogRef: MatDialogRef<EditAssignmentDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any) {
        super(dialogRef, data);
    }

    public ngOnInit() {
        if (this.data.assignment) {
            this.assignment = this.data.assignment;
            if (this.assignment && (this.assignment as any).tag_id) {
                this.selectedTagId = (this.assignment as any).tag_id;
            }
        }
        if (this.data.title) {
            this.title = this.data.title;
        }
        if (this.data.tree) {
            this.tree = this.data.tree;
            // store pristine copy
            this.originalTree = JSON.parse(JSON.stringify(this.tree));
        }
        if (this.data.icons) {
            this.icons = this.data.icons;
        }
        if (this.data.tags) {
            this.teacherTags = this.data.tags;
            if (this.teacherTags.length === 1) {
                this.selectedTagId = this.teacherTags[0].id;
            }
        }
        this.resizeDialog();
    }

    ngAfterViewInit() {
        if (this.selectedTagId) {
            setTimeout(() => this.onTagChange(), 0);
        }
    }

    filterTreeByTag() {
        if (!this.selectedTagId) { return; }
        this.tree.forEach(level => {
            level.children = level.children.filter(u => u.tag_ids && u.tag_ids.indexOf(this.selectedTagId) !== -1);
        });
        // remove levels that ended up with zero children
        this.tree = this.tree.filter(level => level.children && level.children.length > 0);
    }

    onTagChange() {
        // restore from original pristine tree instead of possibly already filtered data.tree
        if (this.originalTree && this.originalTree.length) {
            this.tree = JSON.parse(JSON.stringify(this.originalTree));
        }
        this.filterTreeByTag();
    }

    onSave() {
        this.assignment.tree = $('#tree-form').serialize();
        this.assignment['tag_id'] = this.selectedTagId;
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

    // prevent dialog close on Enter pressed
    @HostListener('document:keypress', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        /* if (event.key === 'Enter') {
            this.dialogRef.close();
        } */
    }

}
