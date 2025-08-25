import {Injectable} from '@angular/core';
import {Observable} from 'rxjs';
import {HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {BehaviorSubject} from 'rxjs';
import {finalize} from 'rxjs/operators';

@Injectable()
export class HTTPStatus {
    private activeRequests = 0;
    private requestInFlight$ = new BehaviorSubject<boolean>(false);

    startRequest() {
        this.activeRequests++;
        if (this.activeRequests === 1) {
            this.requestInFlight$.next(true);
        }
    }

    endRequest() {
        if (this.activeRequests > 0) {
            this.activeRequests--;
            if (this.activeRequests === 0) {
                this.requestInFlight$.next(false);
            }
        } else {
            // Safety: ensure spinner not stuck on due to imbalance
            this.requestInFlight$.next(false);
        }
    }

    getHttpStatus(): Observable<boolean> {
        return this.requestInFlight$.asObservable();
    }
}

@Injectable()
export class HTTPListener implements HttpInterceptor {
    constructor(private status: HTTPStatus) {
    }

    intercept(
        req: HttpRequest<any>,
        next: HttpHandler
    ): Observable<HttpEvent<any>> {
    // Always show spinner for any outgoing HTTP request
    this.status.startRequest();
        return next.handle(req).pipe(
            finalize(() => {
        this.status.endRequest();
            })
        );
    }
}
