import {Injectable} from '@angular/core';

import {HttpService} from './http.service';

@Injectable()
export class ShareService {

    constructor(private http: HttpService) {
    }

    public getShared(type, item_id, filters = null) {
        let url = '/share/' + type + '/' + item_id;
        if (filters) {
            url += '?' + new URLSearchParams(filters).toString();
        }
        return this.http.get(url);
    }

    public addShared(type, item_id, teachers = null) {
        return this.http.post('/share/' + type + '/' + item_id, {
            teachers: teachers
        });
    }

    public deleteShared(type, item_id, teacher_id) {
        return this.http.delete('/share/' + type + '/' + item_id + '/' + teacher_id);
    }

    public getNewShare(type) {
        return this.http.get('/share/' + type + '/new');
    }

    public newShareToggle(type, item_id, is_accepted) {
        return this.http.post('/share/' + type + '/' + item_id + '/' + (is_accepted ? 'accept' : 'decline'));
    }

}
