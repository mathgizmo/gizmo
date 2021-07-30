import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {FormsModule} from '@angular/forms';
import {HttpClientModule, HTTP_INTERCEPTORS} from '@angular/common/http';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {FlexLayoutModule} from '@angular/flex-layout';
import {PerfectScrollbarModule} from 'ngx-perfect-scrollbar';
import {PERFECT_SCROLLBAR_CONFIG} from 'ngx-perfect-scrollbar';

import {MatFormFieldModule} from '@angular/material/form-field';
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
import {MatListModule} from '@angular/material/list';
import {DragDropModule} from '@angular/cdk/drag-drop';
import {ClipboardModule} from '@angular/cdk/clipboard';
import {NgxMatSelectSearchModule} from 'ngx-mat-select-search';
import {CKEditorModule} from '@ckeditor/ckeditor5-angular';

import {AppRoutingModule} from './app.routing';
import {AuthGuard} from './_guards/index';

import {
    AuthenticationService,
    HttpService,
    HTTPListener,
    HTTPStatus,
    CountryService,
    UserService,
    ClassesManagementService
} from './_services/index';

import {AppComponent} from './_components/app.component';
import {
    WelcomeComponent,
    RegisterComponent,
    LoginComponent,
    LogoutComponent,
    ForgotPasswordComponent,
    ResetPasswordComponent,
    TryComponent,
    VerifyEmailComponent
} from './_components/auth/index';
import {AssignmentComponent, TestComponent} from './_components/assignment/index';
import {TopicComponent} from './_components/assignment/topic/index';
import {LessonComponent, ChartComponent} from './_components/assignment/topic/lesson/index';
import {ProfileComponent} from './_components/profile/profile.component';
import {
    GoodDialogComponent,
    BadDialogComponent,
    ReportDialogComponent,
    FeedbackDialogComponent,
    BadChallengeDialogComponent,
    YesNoDialogComponent,
    DeleteConfirmationDialogComponent,
    InfoDialogComponent
} from './_components/dialogs/index';
import {
    MyAssignmentsComponent,
    MyTestsComponent,
    MyClassesComponent,
    MyClassReportComponent,
    MyClassResearchStatusComponent,
    StudentEmailTeacherComponent,
    TestOptionsDialogComponent,
    TestStartDialogComponent,
    MyClassMenuComponent,
    TestReportDialogComponent as StudentTestReportDialogComponent,
    ResearchConsentDialogComponent
} from './_components/student/index';
import {
    ClassReportComponent,
    ClassTeachersComponent,
    ClassResearchersComponent,
    ClassStudentsComponent,
    StudentAssignmentsDialogComponent,
    StudentTestsDialogComponent,
    AddStudentDialogComponent,
    EditClassDialogComponent,
    ManageClassesComponent,
    ManageAssignmentsComponent,
    EditAssignmentDialogComponent,
    AssignmentReportDialogComponent,
    ManageTestsComponent,
    EditTestDialogComponent,
    TestReportDialogComponent,
    TestReportResetAttemptDialogComponent,
    ReviewContentComponent,
    ClassAssignmentsComponent,
    ClassDashboardComponent,
    ClassAssignmentsListComponent,
    ClassTestsListComponent,
    ClassAssignmentsCalendarComponent,
    ClassTestsComponent,
    ClassMenuComponent,
    AssignmentsStudentsUsageChartComponent,
    TestsStudentsUsageChartComponent,
    ClassAssignmentsReportComponent,
    ClassTestsReportComponent,
    EditClassAssignmentDialogComponent,
    EditClassTestDialogComponent,
    SelectStudentsDialogComponent,
    TeacherClassEmailComponent,
    ClassInvitationSettingsComponent
} from './_components/teacher/index';
import {ToDoComponent} from './_components/self_study/index';
import {ClassThreadsComponent, EditThreadDialogComponent} from './_components/class-threads/index';
import {DashboardComponent} from './_components/dashboard/dashboard.component';
import {TutorialComponent} from './_components/tutorial/tutorial.component';
import {FaqComponent} from './_components/faq/faq.component';
import {QuestionComponent} from './_components/assignment/topic/lesson/question/question.component';
import {QuestionPreviewComponent} from './_components/previews/question-preview/question-preview.component';
import {ClassJoinComponent} from './_components/class-join/class-join.component';
// import {PlacementComponent, QuestionNumDialogComponent} from './_components/welcome/placement/index';

import {RecaptchaModule, RecaptchaFormsModule} from 'ng-recaptcha';

import {DraggableDirective} from './_directives/draggable.directive';
import {TableFilterPipe, SafeHtmlPipe, TimeFormatPipe} from './_pipes/index';

import {APP_BASE_HREF, PlatformLocation} from '@angular/common';

import {FullCalendarModule} from '@fullcalendar/angular';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import adaptivePlugin from '@fullcalendar/adaptive';
import scrollGridPlugin from '@fullcalendar/scrollgrid';
import {ServiceWorkerModule} from '@angular/service-worker';
import {environment} from '../environments/environment';

FullCalendarModule.registerPlugins([
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    adaptivePlugin,
    scrollGridPlugin
]);

@NgModule({
    imports: [
        BrowserModule,
        FormsModule,
        HttpClientModule,
        AppRoutingModule,
        BrowserAnimationsModule,
        FullCalendarModule,
        MatFormFieldModule,
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
        MatListModule,
        NgxMatSelectSearchModule,
        DragDropModule,
        ClipboardModule,
        FlexLayoutModule,
        PerfectScrollbarModule,
        RecaptchaModule,
        RecaptchaFormsModule,
        CKEditorModule,
        ServiceWorkerModule.register('ngsw-worker.js', {enabled: environment.production})
    ],
    exports: [],
    declarations: [
        AppComponent,
        WelcomeComponent,
        LoginComponent,
        LogoutComponent,
        VerifyEmailComponent,
        RegisterComponent,
        AssignmentComponent,
        TestComponent,
        TopicComponent,
        LessonComponent,
        GoodDialogComponent,
        BadDialogComponent,
        ReportDialogComponent,
        FeedbackDialogComponent,
        BadChallengeDialogComponent,
        ChartComponent,
        ProfileComponent,
        MyAssignmentsComponent,
        MyTestsComponent,
        MyClassesComponent,
        MyClassMenuComponent,
        MyClassReportComponent,
        MyClassResearchStatusComponent,
        ResearchConsentDialogComponent,
        StudentEmailTeacherComponent,
        ToDoComponent,
        ManageClassesComponent,
        YesNoDialogComponent,
        DeleteConfirmationDialogComponent,
        InfoDialogComponent,
        EditClassDialogComponent,
        StudentAssignmentsDialogComponent,
        StudentTestsDialogComponent,
        AddStudentDialogComponent,
        ManageAssignmentsComponent,
        EditAssignmentDialogComponent,
        AssignmentReportDialogComponent,
        ManageTestsComponent,
        EditTestDialogComponent,
        TestReportDialogComponent,
        TestReportResetAttemptDialogComponent,
        EditClassAssignmentDialogComponent,
        EditClassTestDialogComponent,
        SelectStudentsDialogComponent,
        TestOptionsDialogComponent,
        TestStartDialogComponent,
        StudentTestReportDialogComponent,
        ReviewContentComponent,
        ClassDashboardComponent,
        ClassAssignmentsListComponent,
        ClassTestsListComponent,
        ClassAssignmentsComponent,
        ClassAssignmentsCalendarComponent,
        ClassTestsComponent,
        ClassMenuComponent,
        AssignmentsStudentsUsageChartComponent,
        TestsStudentsUsageChartComponent,
        ClassAssignmentsReportComponent,
        ClassTestsReportComponent,
        TryComponent,
        ForgotPasswordComponent,
        ResetPasswordComponent,
        QuestionComponent,
        QuestionPreviewComponent,
        ClassThreadsComponent,
        DashboardComponent,
        TutorialComponent,
        FaqComponent,
        ClassJoinComponent,
        ClassReportComponent,
        ClassTeachersComponent,
        ClassResearchersComponent,
        ClassStudentsComponent,
        TeacherClassEmailComponent,
        ClassInvitationSettingsComponent,
        EditThreadDialogComponent,
        // PlacementComponent,
        // QuestionNumDialogComponent,
        DraggableDirective,
        TableFilterPipe,
        SafeHtmlPipe,
        TimeFormatPipe
    ],
    entryComponents: [
        GoodDialogComponent,
        BadDialogComponent,
        ReportDialogComponent,
        FeedbackDialogComponent,
        // QuestionNumDialogComponent,
        BadChallengeDialogComponent,
        YesNoDialogComponent,
        DeleteConfirmationDialogComponent,
        InfoDialogComponent,
        EditClassDialogComponent,
        StudentAssignmentsDialogComponent,
        StudentTestsDialogComponent,
        AddStudentDialogComponent,
        EditAssignmentDialogComponent,
        AssignmentReportDialogComponent,
        EditTestDialogComponent,
        TestReportDialogComponent,
        TestReportResetAttemptDialogComponent,
        EditClassAssignmentDialogComponent,
        EditClassTestDialogComponent,
        SelectStudentsDialogComponent,
        TestOptionsDialogComponent,
        TestStartDialogComponent,
        StudentTestReportDialogComponent,
        EditThreadDialogComponent,
        ResearchConsentDialogComponent
    ],
    providers: [
        AuthGuard,
        AuthenticationService,
        UserService,
        ClassesManagementService,
        CountryService,
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
        {
            provide: APP_BASE_HREF,
            useFactory: (s: PlatformLocation) => s.getBaseHrefFromDOM(),
            deps: [PlatformLocation]
        }
    ],
    bootstrap: [AppComponent]
})

export class AppModule {
}
