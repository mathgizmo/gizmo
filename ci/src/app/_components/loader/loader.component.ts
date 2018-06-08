import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { LoaderService } from '../../_services/loader.service';

@Component({
    selector: 'loader',
    templateUrl: 'loader.component.html',
    styleUrls: ['loader.component.scss']
})
export class LoaderComponent implements OnInit {
    show = false;
    private subscription: Subscription;
    constructor(private loaderService: LoaderService) { }

    ngOnInit() { 
        this.subscription = this.loaderService.loaderState
            .subscribe((state: boolean) => {
                this.show = state;
            });
    }

    ngOnDestroy() {
        this.subscription.unsubscribe();
    }
}