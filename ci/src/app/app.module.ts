import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule }    from '@angular/forms';
import { HttpModule } from '@angular/http';

import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MatInputModule, MatButtonModule, MatSelectModule, MatIconModule, MatMenuModule, MatRadioModule, MatDialogModule} from '@angular/material';
import { BaseRequestOptions } from '@angular/http';

import { AppComponent }  from './app.component';
import { routing }        from './app.routing';

import { AuthGuard } from './_guards/index';
import { AuthenticationService, ServerService } from './_services/index';
import { LoginComponent } from './login/index';
import { HomeComponent } from './home/index';
import { TopicComponent } from './topic/index';
import { LessonComponent, GoodDialogComponent, BadDialogComponent } from './lesson/index';
import { Angular2FontawesomeModule } from 'angular2-fontawesome/angular2-fontawesome';
import {FlexLayoutModule} from "@angular/flex-layout";

@NgModule({
    imports: [
        BrowserModule,
        FormsModule,
        HttpModule,
        routing,
        Angular2FontawesomeModule,
        BrowserAnimationsModule,
        MatInputModule, 
        MatButtonModule,
        MatSelectModule,
        MatIconModule,
        MatMenuModule,
        MatRadioModule,
        MatDialogModule,
        FlexLayoutModule
    ],
    declarations: [
        AppComponent,
        LoginComponent,
        HomeComponent,
        TopicComponent,
        LessonComponent,
        GoodDialogComponent,
        BadDialogComponent
    ],
    entryComponents: [
        GoodDialogComponent,
        BadDialogComponent
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