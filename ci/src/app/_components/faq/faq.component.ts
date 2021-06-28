import {Component, OnInit} from '@angular/core';
import {FaqService} from '../../_services/faq.service';

@Component({
    selector: 'app-faq',
    templateUrl: './faq.component.html',
    styleUrls: ['./faq.component.scss'],
    providers: [FaqService]
})
export class FaqComponent implements OnInit {

    public faqs = [];

    constructor(private faqService: FaqService) {}

    ngOnInit() {
        this.faqService.getFaqs()
            .subscribe(res => {
                this.faqs = res;
            });
    }

}
