import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../environments/environment';

export interface Tag {
  id: number;
  name: string;
  order_no?: number;
}

@Injectable({ providedIn: 'root' })
export class TagService {
  private readonly apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getTags(): Observable<Tag[]> {
    return this.http.get<any>(this.apiUrl + '/tags').pipe(
      map(res => (res && res.message ? res.message : []) as Tag[])
    );
  }
}
