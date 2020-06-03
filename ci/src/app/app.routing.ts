import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';

import {WelcomeComponent} from './_components/welcome/index';
import {ForgotPasswordComponent, LoginComponent, ResetPasswordComponent} from './_components/welcome/login/index';
import {RegisterComponent} from './_components/welcome/register/index';
import {HomeComponent} from './_components/home/index';
import {TopicComponent} from './_components/home/topic/index';
import {LessonComponent} from './_components/home/topic/lesson/index';
import {AuthGuard} from './_guards/index';
import {ProfileComponent} from './_components/profile/profile.component';
import {ToDoComponent} from './_components/to-do/to-do.component';
import {MyClassesComponent} from './_components/classes/my-classes/my-classes.component';
import {ManageClassesComponent} from './_components/classes/manage-classes/manage-classes.component';
import {ManageAssignmentsComponent} from './_components/assignments/manage-assignments/manage-assignments.component';
import {PlacementComponent} from './_components/welcome/placement/placement.component';
import {QuestionPreviewComponent} from './_components/previews/index';

const routes: Routes = [
    {path: 'welcome', component: WelcomeComponent},
    {path: 'login', component: LoginComponent},
    {path: 'register', component: RegisterComponent},
    {path: 'forgot-password', component: ForgotPasswordComponent},
    {path: 'reset-password/:token', component: ResetPasswordComponent},
    {path: 'placement', component: PlacementComponent, canActivate: [AuthGuard]},
    {path: 'profile', component: ProfileComponent, canActivate: [AuthGuard]},
    {path: 'to-do', component: ToDoComponent, canActivate: [AuthGuard]},
    {path: 'my-classes', component: MyClassesComponent, canActivate: [AuthGuard]},
    {path: 'manage-classes', component: ManageClassesComponent, canActivate: [AuthGuard], data: { roles: ['teacher', 'admin'] }
    },
    {path: 'manage-assignments', component: ManageAssignmentsComponent, canActivate: [AuthGuard], data: { roles: ['teacher', 'admin'] }
    },
    {path: '', component: HomeComponent, canActivate: [AuthGuard]},
    {path: 'topic/:id', component: TopicComponent, canActivate: [AuthGuard]},
    {
        path: 'topic/:topic_id/lesson/:lesson_id', component: LessonComponent,
        canActivate: [AuthGuard]
    },
    {path: 'preview/question', component: QuestionPreviewComponent},
    // otherwise redirect to welcome
    {path: '**', redirectTo: 'welcome'}
];

@NgModule({
    imports: [
        RouterModule.forRoot(routes, {
            scrollPositionRestoration: 'enabled',
            anchorScrolling: 'enabled'
        })
    ],
    exports: [
        RouterModule
    ]
})
export class AppRoutingModule {
}
