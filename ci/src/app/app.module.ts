import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule }    from '@angular/forms';
import { HttpModule } from '@angular/http';

// used to create fake backend
//import { fakeBackendProvider } from './_helpers/index';
//import { MockBackend, MockConnection } from '@angular/http/testing';
import { BaseRequestOptions } from '@angular/http';

import { AppComponent }  from './app.component';
import { routing }        from './app.routing';

import { AuthGuard } from './_guards/index';
import { AuthenticationService, ServerService } from './_services/index';
import { LoginComponent } from './login/index';
import { HomeComponent } from './home/index';
import { TopicComponent } from './topic/index';
import { Angular2FontawesomeModule } from 'angular2-fontawesome/angular2-fontawesome'


@NgModule({
    imports: [
        BrowserModule,
        FormsModule,
        HttpModule,
        routing,
        Angular2FontawesomeModule
    ],
    declarations: [
        AppComponent,
        LoginComponent,
        HomeComponent,
        TopicComponent
    ],
    providers: [
        AuthGuard,
        AuthenticationService,
        ServerService,

        // providers used to create fake backend
        //fakeBackendProvider,
        //MockBackend,
        BaseRequestOptions
    ],
    bootstrap: [AppComponent]
})

export class AppModule { }