import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';

import {WelcomeComponent, RegisterComponent, LoginComponent,
    ForgotPasswordComponent, ResetPasswordComponent} from './_components/auth/index';
import {AssignmentComponent} from './_components/assignment/index';
import {TopicComponent} from './_components/assignment/topic/index';
import {LessonComponent} from './_components/assignment/topic/lesson/index';
import {AuthGuard} from './_guards/index';
import {ProfileComponent} from './_components/profile/profile.component';
import {ToDoComponent} from './_components/student/to-do/to-do.component';
import {MyClassesComponent} from './_components/student/my-classes/my-classes.component';
import {MyInvitationsComponent} from './_components/student/my-invitations/my-invitations.component';
import {ManageClassesComponent} from './_components/teacher/manage-classes/manage-classes.component';
import {ManageAssignmentsComponent} from './_components/teacher/manage-assignments/manage-assignments.component';
// import {PlacementComponent} from './_components/welcome/placement/placement.component';
import {QuestionPreviewComponent} from './_components/previews/index';
import {DashboardComponent} from './_components/teacher/dashboard/dashboard.component';

const teacherRoutes = [
    {path: 'manage-classes', component: ManageClassesComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'manage-assignments', component: ManageAssignmentsComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
    {path: 'dashboard', component: DashboardComponent, canActivate: [AuthGuard], data: {roles: ['teacher']}},
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
