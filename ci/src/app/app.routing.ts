import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';

import {AuthGuard} from './_guards/index';

import {WelcomeComponent, RegisterComponent, LoginComponent, LogoutComponent,
    ForgotPasswordComponent, ResetPasswordComponent, VerifyEmailComponent} from './_components/auth/index';
import {AssignmentComponent, TestComponent, TopicComponent, LessonComponent} from './_components/assignment/index';
import {ProfileComponent} from './_components/profile/profile.component';
import { MyAssignmentsComponent, MyTestsComponent, MyClassesComponent,
    MyInvitationsComponent, MyClassReportComponent, StudentEmailTeacherComponent
} from './_components/student/index';
import {ClassThreadsComponent} from './_components/class-threads/class-threads.component';
// import {PlacementComponent} from './_components/welcome/placement/placement.component';
import {QuestionPreviewComponent} from './_components/previews/index';
import {DashboardComponent} from './_components/dashboard/dashboard.component';
import {TutorialComponent} from './_components/tutorial/tutorial.component';
import {FaqComponent} from './_components/faq/faq.component';
import {
    ClassReportComponent, ManageAssignmentsComponent, ManageTestsComponent,
    ManageClassesComponent, ReviewContentComponent, ClassDashboardComponent,
    ClassAssignmentsComponent, ClassTestsComponent, ClassStudentsComponent,
    ClassTeachersComponent, TeacherClassEmailComponent, ClassInvitationSettingsComponent
} from './_components/teacher/index';
import {ToDoComponent} from './_components/self_study';

const authRoutes = [
    {path: 'welcome', component: WelcomeComponent},
    {path: 'login', component: LoginComponent},
    {path: 'register', component: RegisterComponent},
    {path: 'logout', component: LogoutComponent},
    {path: 'verify-email', component: VerifyEmailComponent},
    {path: 'forgot-password', component: ForgotPasswordComponent},
    {path: 'reset-password/:token', component: ResetPasswordComponent},
    // {path: 'placement', component: PlacementComponent, canActivate: [AuthGuard]},
];

const teacherRoutes = [
    {path: 'teacher/class', component: ManageClassesComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/review-content', component: ReviewContentComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/assignment', component: ManageAssignmentsComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/test', component: ManageTestsComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/assignments', component: ClassAssignmentsComponent,
        canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/tests', component: ClassTestsComponent,
        canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/report', component: ClassReportComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/threads', component: ClassThreadsComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/dashboard', component: ClassDashboardComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/students', component: ClassStudentsComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/teachers', component: ClassTeachersComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/email', component: TeacherClassEmailComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/invitation-settings', component: ClassInvitationSettingsComponent,
        canActivate: [AuthGuard], data: {roles: ['teacher']}},
];

const studentRoutes = [
    {path: 'student/class', component: MyClassesComponent, canActivate: [AuthGuard], data: {roles: ['student']}},
    {path: 'student/class/:class_id/report', component: MyClassReportComponent, canActivate: [AuthGuard], data: {roles: ['student']}},
    {path: 'student/class/:class_id/threads', component: ClassThreadsComponent, canActivate: [AuthGuard], data: {roles: ['student']}},
    {path: 'student/class/:class_id/assignments', component: MyAssignmentsComponent, canActivate: [AuthGuard], data: {roles: ['student']}},
    {path: 'student/class/:class_id/tests', component: MyTestsComponent, canActivate: [AuthGuard], data: {roles: ['student']}},
    {path: 'student/class/:class_id/email', component: StudentEmailTeacherComponent, canActivate: [AuthGuard], data: {roles: ['student']}},
    {path: 'student/class/:class_id/test/:test_id', component: TestComponent, canActivate: [AuthGuard]},
    {path: 'student/invitations', component: MyInvitationsComponent, canActivate: [AuthGuard], data: {roles: ['student']}},
];

const routes: Routes = [
    ...authRoutes,
    {path: '', component: AssignmentComponent, canActivate: [AuthGuard]},
    {path: 'to-do', component: ToDoComponent, canActivate: [AuthGuard]},
    {path: 'assignment/:assignment_id', component: AssignmentComponent, canActivate: [AuthGuard]},
    {path: 'assignment/:assignment_id/topic/:topic_id', component: TopicComponent, canActivate: [AuthGuard]},
    {path: 'assignment/:assignment_id/topic/:topic_id/lesson/:lesson_id', component: LessonComponent, canActivate: [AuthGuard]},
    {path: 'profile', component: ProfileComponent, canActivate: [AuthGuard]},
    {path: 'dashboard', component: DashboardComponent, canActivate: [AuthGuard], data: {roles: ['student', 'teacher']}},
    {path: 'tutorial', component: TutorialComponent, canActivate: [AuthGuard], data: {roles: ['student', 'teacher']}},
    {path: 'faq', component: FaqComponent, canActivate: [AuthGuard], data: {roles: ['student', 'teacher']}},
    ...studentRoutes,
    ...teacherRoutes,
    {path: 'preview/question', component: QuestionPreviewComponent},
    {path: '**', redirectTo: 'welcome'}
];

@NgModule({
    imports: [
        RouterModule.forRoot(routes, {
            scrollPositionRestoration: 'enabled',
            anchorScrolling: 'enabled',
            onSameUrlNavigation: 'reload'
        })
    ],
    exports: [
        RouterModule
    ]
})
export class AppRoutingModule {
}
