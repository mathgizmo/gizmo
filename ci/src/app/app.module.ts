import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {FormsModule} from '@angular/forms';
import {HttpClientModule, HTTP_INTERCEPTORS} from '@angular/common/http';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {FlexLayoutModule} from '@angular/flex-layout';
import {DeviceDetectorModule} from 'ngx-device-detector';
import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';
import { PERFECT_SCROLLBAR_CONFIG } from 'ngx-perfect-scrollbar';

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
import {MatSortModule} from '@angular/material/sort';
import {MatSnackBarModule} from '@angular/material/snack-bar';
import { DragDropModule } from '@angular/cdk/drag-drop';

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
import {ToDoComponent} from './_components/to-do/to-do.component';
import {YesNoDialogComponent} from './_components/classes/yes-no-dialog/yes-no-dialog.component';
import {MyClassesComponent} from './_components/classes/my-classes/my-classes.component';
import {MyInvitationsComponent} from './_components/classes/my-invitations/my-invitations.component';
import {ClassStudentsDialogComponent} from './_components/classes/manage-classes/class-students-dialog/class-students-dialog.component';
import {StudentAssignmentsDialogComponent} from './_components/classes/manage-classes/class-students-dialog/student-assignments-dialog/student-assignments-dialog.component';
import {EditClassDialogComponent} from './_components/classes/manage-classes/edit-class-dialog/edit-class-dialog.component';
import {ManageClassesComponent} from './_components/classes/manage-classes/manage-classes.component';
import {ClassAssignmentsDialogComponent} from './_components/classes/manage-classes/class-assignments-dialog/class-assignments-dialog.component';
import {ManageAssignmentsComponent} from './_components/assignments/manage-assignments/manage-assignments.component';
import {EditAssignmentDialogComponent} from './_components/assignments/manage-assignments/edit-assignment-dialog/edit-assignment-dialog.component'
import {ResetPasswordComponent} from './_components/welcome/login/reset-password/reset-password.component';
import {QuestionComponent} from './_components/home/topic/lesson/question/question.component';
import {QuestionPreviewComponent} from './_components/previews/question-preview/question-preview.component';
// import {PlacementComponent, QuestionNumDialogComponent} from './_components/welcome/placement/index';

import {DraggableDirective} from './_directives/draggable.directive';
import { TableFilterPipe, SafeHtmlPipe } from './_pipes/index';

@NgModule({
    imports: [
        BrowserModule,
        FormsModule,
        HttpClientModule,
        AppRoutingModule,
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
        MatSortModule,
        MatSnackBarModule,
        DragDropModule,
        FlexLayoutModule,
        PerfectScrollbarModule,
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
        ToDoComponent,
        MyClassesComponent,
        MyInvitationsComponent,
        ManageClassesComponent,
        YesNoDialogComponent,
        EditClassDialogComponent,
        ClassStudentsDialogComponent,
        StudentAssignmentsDialogComponent,
        ClassAssignmentsDialogComponent,
        ManageAssignmentsComponent,
        EditAssignmentDialogComponent,
        TryComponent,
        ForgotPasswordComponent,
        ResetPasswordComponent,
        QuestionComponent,
        QuestionPreviewComponent,
        // PlacementComponent,
        // QuestionNumDialogComponent,
        DraggableDirective,
        TableFilterPipe,
        SafeHtmlPipe
    ],
    entryComponents: [
        GoodDialogComponent,
        BadDialogComponent,
        ReportDialogComponent,
        FeedbackDialogComponent,
        // QuestionNumDialogComponent,
        BadChallengeDialogComponent,
        YesNoDialogComponent,
        EditClassDialogComponent,
        ClassStudentsDialogComponent,
        StudentAssignmentsDialogComponent,
        ClassAssignmentsDialogComponent,
        EditAssignmentDialogComponent
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
        {
            provide: PERFECT_SCROLLBAR_CONFIG,
            useValue: {
                suppressScrollX: true
            }
        },
    ],
    bootstrap: [AppComponent]
})

export class AppModule {
}
