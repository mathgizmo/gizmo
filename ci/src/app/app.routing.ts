import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';

import {AuthGuard} from './_guards/index';

import {WelcomeComponent, RegisterComponent, LoginComponent,
    ForgotPasswordComponent, ResetPasswordComponent} from './_components/auth/index';
import {AssignmentComponent, TopicComponent, LessonComponent} from './_components/assignment/index';
import {ProfileComponent} from './_components/profile/profile.component';
import {ToDoComponent, MyClassesComponent, MyInvitationsComponent} from './_components/student/index';
// import {PlacementComponent} from './_components/welcome/placement/placement.component';
import {QuestionPreviewComponent} from './_components/previews/index';
import {DashboardComponent, ClassReportComponent, ManageAssignmentsComponent,
    ManageClassesComponent, ReviewContentComponent, ClassDashboardComponent,
    ClassAssignmentsComponent, ClassStudentsComponent, ClassToDoComponent} from './_components/teacher/index';

const teacherRoutes = [
    {path: 'teacher/dashboard', component: DashboardComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class', component: ManageClassesComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/review-content', component: ReviewContentComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/assignment', component: ManageAssignmentsComponent,
        canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/assignments', component: ClassAssignmentsComponent,
        canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/report', component: ClassReportComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/dashboard', component: ClassDashboardComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/students', component: ClassStudentsComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'teacher/class/:class_id/to-do', component: ClassToDoComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
];

const studentRoutes = [
    {path: 'to-do', component: ToDoComponent, canActivate: [AuthGuard]},
    {path: 'my-classes', component: MyClassesComponent, canActivate: [AuthGuard]},
    {path: 'my-invitations', component: MyInvitationsComponent, canActivate: [AuthGuard]},
];

const routes: Routes = [
    {path: 'welcome', component: WelcomeComponent},
    {path: 'login', component: LoginComponent},
    {path: 'register', component: RegisterComponent},
    {path: 'forgot-password', component: ForgotPasswordComponent},
    {path: 'reset-password/:token', component: ResetPasswordComponent},
    // {path: 'placement', component: PlacementComponent, canActivate: [AuthGuard]},
    {path: '', component: AssignmentComponent, canActivate: [AuthGuard]},
    {path: 'topic/:id', component: TopicComponent, canActivate: [AuthGuard]},
    {path: 'topic/:topic_id/lesson/:lesson_id', component: LessonComponent, canActivate: [AuthGuard]},
    {path: 'profile', component: ProfileComponent, canActivate: [AuthGuard]},
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
