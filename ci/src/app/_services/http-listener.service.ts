import {Injectable} from '@angular/core';
import {Observable} from 'rxjs';
import {
    HttpEvent,
    HttpHandler,
    HttpInterceptor,
    HttpRequest
} from '@angular/common/http';

import {BehaviorSubject, throwError} from 'rxjs';
import {catchError, finalize, map} from 'rxjs/operators';

@Injectable()
export class HTTPStatus {
    private requestInFlight$: BehaviorSubject<boolean> = new BehaviorSubject(false);

    constructor() {
    }

    setHttpStatus(inFlight: boolean) {
        this.requestInFlight$.next(inFlight);
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
        const hideLoader = req.params.get('hideLoader') || false;
        if (hideLoader) {
            return next.handle(req);
        }
        this.status.setHttpStatus(true);
        return next.handle(req).pipe(
            /* map(event => {
                return event;
            }),
            catchError(error => {
                return throwError(error);
            }), */
            finalize(() => {
                this.status.setHttpStatus(false);
            })
        );
    }
}
