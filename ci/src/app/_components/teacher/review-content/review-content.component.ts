import {Component, OnInit} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';

import {ContentService, UserService} from '../../../_services';
import {environment} from '../../../../environments/environment';
import {Router} from '@angular/router';

@Component({
    moduleId: module.id,
    templateUrl: 'review-content.component.html',
    providers: [ContentService],
    styleUrls: ['review-content.component.scss']
})

export class ReviewContentComponent implements OnInit {
    topicsTree: any = [];
    private allTopicsTree: any = [];
    // tag filtering
    public teacherTags: any[] = [];
    public selectedTagIds: number[] = [];
    private readonly adminUrl = environment.adminUrl;

    constructor(private router: Router,
                private contentService: ContentService,
                private sanitizer: DomSanitizer,
                private userService: UserService) {
    }

    ngOnInit() {
        // load teacher's available tags for filtering
        this.userService.getProfile().subscribe((res: any) => {
            if (res && res.tags) {
                this.teacherTags = res.tags;
                // default: select all available tags
                if (this.teacherTags.length) {
                    this.selectedTagIds = this.teacherTags.map(t => +t.id).filter(x => !isNaN(x));
                }
                // re-apply if content already loaded
                this.applyTagFilter();
            }
        });

        // load content tree
        this.contentService.getContent()
            .subscribe(res => {
                this.allTopicsTree = res;
                // apply with current selection (may be all tags if loaded)
                this.applyTagFilter();

                const lastVisitedTopic = +localStorage.getItem('last-visited-topic-id');
                if (lastVisitedTopic) {
                    for (const item of this.topicsTree) {
                        for (const unit of item.units) {
                            unit.show = false;
                            for (const topic of unit.topics) {
                                if (topic.id === lastVisitedTopic) {
                                    setTimeout(() => {
                                        $('#unit' + unit.id + '-topics').slideDown('slow');
                                        $('#topic' + lastVisitedTopic + '-lessons').slideDown('slow');
                                        $('html, body').animate({
                                            scrollTop: ($('#topic' + lastVisitedTopic).offset().top) - 8
                                        }, 1000);
                                    }, 100);
                                    topic.show = true;
                                    unit.show = true;
                                } else {
                                    topic.show = false;
                                }
                            }
                        }
                    }
                }
            });
    }

    slideToggle(item: any) {
        $('#unit' + item.id + '-topics').slideToggle('slow');
        item.show = !item.show;
    }

    slideToggleTopic(item: any) {
        $('#topic' + item.id + '-lessons').slideToggle('slow');
        item.show = !item.show;
    }

    setTopicIcon(image) {
        const link = `url(` + this.adminUrl + `/${image})`;
        return this.sanitizer.bypassSecurityTrustStyle(link);
    }

    // Apply selected tag filter to the content tree (filters units by tag_ids)
    applyTagFilter() {
        const selected = this.selectedTagIds || [];
        const source = this.allTopicsTree || [];
        // If nothing selected, show nothing
        if (!selected.length) { this.topicsTree = []; return; }
        // If content has no tag data at all, keep unfiltered view
        const anyUnitHasTags = source.some(level => (level.units || []).some(u => this.getUnitTagIds(u).length > 0));
        if (!anyUnitHasTags) {
            this.topicsTree = JSON.parse(JSON.stringify(source));
            return;
        }
        this.topicsTree = source.map(level => {
            const levelCopy = { ...level };
            levelCopy.units = (level.units || []).filter(u => {
                const unitTags: number[] = this.getUnitTagIds(u);
                return unitTags.some(tid => selected.indexOf(tid) !== -1);
            })
                .map(u => ({ ...u }));
            return levelCopy;
        }).filter(l => (l.units && l.units.length));
    }

    clearTagSelection() {
        this.selectedTagIds = [];
        this.applyTagFilter();
    }

    toggleTag(tagId: number) {
        const id = +tagId;
        const exists = this.selectedTagIds.indexOf(id) !== -1;
        this.selectedTagIds = exists
            ? this.selectedTagIds.filter(x => x !== id)
            : [...this.selectedTagIds, id];
        this.applyTagFilter();
    }

    private getUnitTagIds(u: any): number[] {
        if (!u) { return []; }
        // Case 1: array of ids
        if (Array.isArray(u.tag_ids)) {
            return (u.tag_ids as any[]).map(x => +x).filter(x => !isNaN(x));
        }
        // Case 2: array of tag objects with id
        if (Array.isArray(u.tags)) {
            return (u.tags as any[]).map(t => +(t && (t.id ?? t.tag_id))).filter(x => !isNaN(x));
        }
        // Case 3: single tag id property
        const single = +(u.tag_id ?? u.tagId);
        if (!isNaN(single)) { return [single]; }
        return [];
    }
}
