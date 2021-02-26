import {Injectable} from '@angular/core';
import {map, catchError} from 'rxjs/operators';
import {HttpService} from './http.service';

@Injectable()
export class ClassThreadsService {

    constructor(
        private http: HttpService) {
    }

    public getThreads(class_id) {
        return this.http.get('/classes/' + class_id + '/threads');
    }

    public addThread(class_id, title, message) {
        return this.http.post('/classes/' + class_id + '/threads', {
            title: title,
            message: message
        }).pipe(
            map((response: Response) => {
                return response['item'];
            }),
            catchError(error => {
                console.log(error);
                throw Error(error);
            })
        );
    }

    public updateThread(class_id, thread_id, title, message) {
        return this.http.put('/classes/' + class_id + '/threads/' + thread_id, {
            title: title,
            message: message
        });
    }

    public deleteThread(class_id, thread_id) {
        return this.http.delete('/classes/' + class_id + '/threads/' + thread_id);
    }

    public getThreadReplies(class_id, thread_id) {
        return this.http.get('/classes/' + class_id + '/threads/' + thread_id);
    }

    public addThreadReply(class_id, thread_id, message) {
        return this.http.post('/classes/' + class_id + '/threads/' + thread_id + '/reply', {
            message: message
        }).pipe(
            map((response: Response) => {
                return response['item'];
            }),
            catchError(error => {
                console.log(error);
                throw Error(error);
            })
        );
    }

    public updateThreadReply(class_id, thread_id, reply_id, message) {
        return this.http.put('/classes/' + class_id + '/threads/' + thread_id + '/reply/' + reply_id, {
            message: message
        });
    }

    public deleteThreadReply(class_id, thread_id, reply_id) {
        return this.http.delete('/classes/' + class_id + '/threads/' + thread_id + '/reply/' + reply_id);
    }

}
