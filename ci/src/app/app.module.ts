import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {FormsModule} from '@angular/forms';
import {HttpClientModule, HTTP_INTERCEPTORS} from '@angular/common/http';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {FlexLayoutModule} from '@angular/flex-layout';
import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';
import { PERFECT_SCROLLBAR_CONFIG } from 'ngx-perfect-scrollbar';

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
import {NgxMatSelectSearchModule} from 'ngx-mat-select-search';

import {AppRoutingModule} from './app.routing';
import {AuthGuard} from './_guards/index';

import {AuthenticationService, HttpService, HTTPListener, HTTPStatus, CountryService} from './_services/index';

import {AppComponent} from './_components/app.component';
import {WelcomeComponent, RegisterComponent, LoginComponent, LogoutComponent,
    ForgotPasswordComponent, ResetPasswordComponent, TryComponent, VerifyEmailComponent} from './_components/auth/index';
import {AssignmentComponent, TestComponent} from './_components/assignment/index';
import {TopicComponent} from './_components/assignment/topic/index';
import {LessonComponent, ChartComponent} from './_components/assignment/topic/lesson/index';
import {ProfileComponent} from './_components/profile/profile.component';
import {GoodDialogComponent, BadDialogComponent, ReportDialogComponent,
    FeedbackDialogComponent, BadChallengeDialogComponent, YesNoDialogComponent, DeleteConfirmationDialogComponent} from './_components/dialogs/index';
import {ToDoComponent, MyTestsComponent, MyClassesComponent, MyClassReportComponent,
    MyInvitationsComponent, TestOptionsDialogComponent, TestStartDialogComponent, TestReportDialogComponent as StudentTestReportDialogComponent} from './_components/student/index';
import {ClassReportComponent, ClassStudentsComponent,
    StudentAssignmentsDialogComponent, AddStudentDialogComponent,
    EditClassDialogComponent, ManageClassesComponent, ManageAssignmentsComponent,
    EditAssignmentDialogComponent, ManageTestsComponent, EditTestDialogComponent, TestReportDialogComponent, TestReportResetAttemptDialogComponent,
    ReviewContentComponent, ClassDashboardComponent, ClassAssignmentsComponent,
    ClassAssignmentsCalendarComponent, ClassTestsComponent, ClassToDoComponent, ClassMenuComponent,
    StudentsUsageChartComponent, StudentsUsageBarChartComponent, ClassDetailedReportComponent, ClassTestsReportComponent,
    EditClassAssignmentDialogComponent, EditClassTestDialogComponent, SelectStudentsDialogComponent} from './_components/teacher/index';
import {DashboardComponent} from './_components/dashboard/dashboard.component';
import {QuestionComponent} from './_components/assignment/topic/lesson/question/question.component';
import {QuestionPreviewComponent} from './_components/previews/question-preview/question-preview.component';
// import {PlacementComponent, QuestionNumDialogComponent} from './_components/welcome/placement/index';

import { RecaptchaModule, RecaptchaFormsModule } from 'ng-recaptcha';

import {DraggableDirective} from './_directives/draggable.directive';
import { TableFilterPipe, SafeHtmlPipe, TimeFormatPipe } from './_pipes/index';

import { ChartsModule } from 'ng2-charts';

import { FullCalendarModule } from '@fullcalendar/angular';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import adaptivePlugin from '@fullcalendar/adaptive';
import scrollGridPlugin from '@fullcalendar/scrollgrid';
import { ServiceWorkerModule } from '@angular/service-worker';
import { environment } from '../environments/environment';

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
        ChartsModule,
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
        FlexLayoutModule,
        PerfectScrollbarModule,
        RecaptchaModule,
        RecaptchaFormsModule,
        ServiceWorkerModule.register('ngsw-worker.js', { enabled: environment.production })
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
        ToDoComponent,
        MyTestsComponent,
        MyClassesComponent,
        MyClassReportComponent,
        MyInvitationsComponent,
        ManageClassesComponent,
        YesNoDialogComponent,
        DeleteConfirmationDialogComponent,
        EditClassDialogComponent,
        StudentAssignmentsDialogComponent,
        AddStudentDialogComponent,
        ManageAssignmentsComponent,
        EditAssignmentDialogComponent,
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
        ClassAssignmentsComponent,
        ClassAssignmentsCalendarComponent,
        ClassTestsComponent,
        ClassToDoComponent,
        ClassMenuComponent,
        StudentsUsageChartComponent,
        StudentsUsageBarChartComponent,
        ClassDetailedReportComponent,
        ClassTestsReportComponent,
        TryComponent,
        ForgotPasswordComponent,
        ResetPasswordComponent,
        QuestionComponent,
        QuestionPreviewComponent,
        DashboardComponent,
        ClassReportComponent,
        ClassStudentsComponent,
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
        EditClassDialogComponent,
        StudentAssignmentsDialogComponent,
        AddStudentDialogComponent,
        EditAssignmentDialogComponent,
        EditTestDialogComponent,
        TestReportDialogComponent,
        TestReportResetAttemptDialogComponent,
        EditClassAssignmentDialogComponent,
        EditClassTestDialogComponent,
        SelectStudentsDialogComponent,
        TestOptionsDialogComponent,
        TestStartDialogComponent,
        StudentTestReportDialogComponent
    ],
    providers: [
        AuthGuard,
        AuthenticationService,
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
    ],
    bootstrap: [AppComponent]
})

export class AppModule {
}
