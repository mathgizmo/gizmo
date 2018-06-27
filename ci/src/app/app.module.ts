import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MatInputModule, MatButtonModule, MatSelectModule, 
    MatIconModule, MatMenuModule, MatRadioModule, 
    MatDialogModule, MatProgressBarModule, MatSliderModule,
    MatToolbarModule, MatCardModule, MatCheckboxModule } from '@angular/material';
import { Angular2FontawesomeModule } from 'angular2-fontawesome/angular2-fontawesome';
import {FlexLayoutModule} from "@angular/flex-layout";
import { SortablejsModule } from 'angular-sortablejs';

import { BaseRequestOptions, HttpModule } from '@angular/http'; // ??? Fake BackEnd

import { routing } from './app.routing';
import { AuthGuard } from './_guards/index';

import { AuthenticationService, HttpService, 
    HTTPListener, HTTPStatus } from './_services/index';

import { AppComponent } from './_components/app.component';
import { WelcomeComponent } from './_components/welcome/index';
import { LoginComponent, ForgotPasswordComponent } from './_components/welcome/login/index';
import { RegisterComponent } from './_components/welcome/register/index';
import { TryComponent } from './_components/welcome/try/try.component';
import { HomeComponent } from './_components/home/index';
import { TopicComponent } from './_components/home/topic/index';
import { LessonComponent, GoodDialogComponent, BadDialogComponent, 
    ReportDialogComponent, ChartComponent } 
    from './_components/home/topic/lesson/index';
import { ProfileComponent } from './_components/profile/profile.component';
import { ResetPasswordComponent } from './_components/welcome/login/reset-password/reset-password.component';
import { QuestionComponent } from './_components/home/topic/lesson/question/question.component';

import { QuestionPreviewComponent } from
    './_components/previews/question-preview/question-preview.component';
import { PlacementComponent, QuestionNumDialogComponent } from './_components/welcome/placement/index';

@NgModule({
    imports: [
        BrowserModule,
        FormsModule,
        HttpClientModule,
        HttpModule, // ??? Fake BackEnd
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
        MatProgressBarModule,
        MatSliderModule,
        MatToolbarModule, 
        MatCardModule,
        MatCheckboxModule,
        FlexLayoutModule,
        SortablejsModule.forRoot({ animation: 150 })
    ],
    exports: [
    ],
    declarations: [
        AppComponent,
        WelcomeComponent,
        LoginComponent,
        RegisterComponent,
        HomeComponent,
        TopicComponent,
        LessonComponent,
        GoodDialogComponent,
        BadDialogComponent,
        ReportDialogComponent,
        ChartComponent,
        ProfileComponent,
        TryComponent,
        ForgotPasswordComponent,
        ResetPasswordComponent,
        QuestionComponent,
        QuestionPreviewComponent,
        PlacementComponent,
        QuestionNumDialogComponent
    ],
    entryComponents: [
        GoodDialogComponent,
        BadDialogComponent,
        ReportDialogComponent,
        QuestionNumDialogComponent
    ],
    providers: [
        AuthGuard,
        AuthenticationService,
        HttpService,
        HTTPListener, 
        HTTPStatus,
        {
          provide: HTTP_INTERCEPTORS,
          useClass: HTTPListener,
          multi: true
        },

        // providers used to create fake backend
        //fakeBackendProvider,
        //MockBackend,
        BaseRequestOptions
    ],
    bootstrap: [AppComponent]
})

export class AppModule { }