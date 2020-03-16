import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {FormsModule} from '@angular/forms';
import {HttpClientModule, HTTP_INTERCEPTORS} from '@angular/common/http';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {MatInputModule} from '@angular/material/input';
import {MatButtonModule} from '@angular/material/button';
import {MatSelectModule} from '@angular/material/select';
import {MatIconModule} from '@angular/material/icon';
import {MatMenuModule} from '@angular/material/menu';
import {MatRadioModule} from '@angular/material/radio';
import {MatDialogModule} from '@angular/material/dialog';
import {MatProgressBarModule} from '@angular/material/progress-bar';
import {MatSliderModule} from '@angular/material/slider';
import {MatToolbarModule} from '@angular/material/toolbar';
import {MatCardModule} from '@angular/material/card';
import {MatCheckboxModule} from '@angular/material/checkbox';
import {MatDividerModule} from '@angular/material/divider';
import {MatButtonToggleModule} from '@angular/material/button-toggle';
import {Angular2FontawesomeModule} from 'angular2-fontawesome/angular2-fontawesome';
import {FlexLayoutModule} from '@angular/flex-layout';
import {SortablejsModule} from 'ngx-sortablejs';
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
    ReportDialogComponent, FeedbackDialogComponent, BadChallengeDialogComponent, ChartComponent
} from './_components/home/topic/lesson/index';
import {ProfileComponent} from './_components/profile/profile.component';
import {ProfileApplicationComponent} from './_components/profile/application/application.component';
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
        MatButtonToggleModule,
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
        BadChallengeDialogComponent,
        ChartComponent,
        ProfileComponent,
        ProfileApplicationComponent,
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
        QuestionNumDialogComponent,
        BadChallengeDialogComponent
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
