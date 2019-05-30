import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {FormsModule} from '@angular/forms';
import {HttpClientModule, HTTP_INTERCEPTORS} from '@angular/common/http';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {
    MatInputModule, MatButtonModule, MatSelectModule,
    MatIconModule, MatMenuModule, MatRadioModule,
    MatDialogModule, MatProgressBarModule, MatSliderModule,
    MatToolbarModule, MatCardModule, MatCheckboxModule, MatDividerModule
} from '@angular/material';
import {Angular2FontawesomeModule} from 'angular2-fontawesome/angular2-fontawesome';
import {FlexLayoutModule} from '@angular/flex-layout';
import {SortablejsModule} from 'angular-sortablejs';
import {DeviceDetectorModule} from 'ngx-device-detector';

import {AppRoutingModule} from './app.routing';
import {AuthGuard} from './_guards/index';

import {AuthenticationService, HttpService, HTTPListener, HTTPStatus} from './_services/index';

import {AppComponent} from './_components/app.component';
import {WelcomeComponent} from './_components/welcome/index';
import {LoginComponent, ForgotPasswordComponent} from './_components/welcome/login/index';
import {RegisterComponent} from './_components/welcome/register/index';
import {TryComponent} from './_components/welcome/try/try.component';
import {HomeComponent} from './_components/home/index';
import {TopicComponent} from './_components/home/topic/index';
import {
    LessonComponent, GoodDialogComponent, BadDialogComponent,
    ReportDialogComponent, FeedbackDialogComponent, ChartComponent
} from './_components/home/topic/lesson/index';
import {ProfileComponent} from './_components/profile/profile.component';
import {ResetPasswordComponent} from './_components/welcome/login/reset-password/reset-password.component';
import {QuestionComponent} from './_components/home/topic/lesson/question/question.component';

import {QuestionPreviewComponent} from './_components/previews/question-preview/question-preview.component';
import {PlacementComponent, QuestionNumDialogComponent} from './_components/welcome/placement/index';

import {DraggableDirective} from './_directives/draggable.directive';

@NgModule({
    imports: [
        BrowserModule,
        FormsModule,
        HttpClientModule,
        AppRoutingModule,
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
        MatDividerModule,
        FlexLayoutModule,
        SortablejsModule.forRoot({animation: 150}),
        DeviceDetectorModule.forRoot()
    ],
    exports: [],
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
        FeedbackDialogComponent,
        ChartComponent,
        ProfileComponent,
        TryComponent,
        ForgotPasswordComponent,
        ResetPasswordComponent,
        QuestionComponent,
        QuestionPreviewComponent,
        PlacementComponent,
        QuestionNumDialogComponent,
        DraggableDirective
    ],
    entryComponents: [
        GoodDialogComponent,
        BadDialogComponent,
        ReportDialogComponent,
        FeedbackDialogComponent,
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
    ],
    bootstrap: [AppComponent]
})

export class AppModule {
}
