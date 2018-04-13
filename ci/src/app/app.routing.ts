import { Routes, RouterModule } from '@angular/router';

import { WelcomeComponent } from './_components/welcome/index';
import { LoginComponent, ForgotPasswordComponent } from './_components/welcome/login/index';
import { RegisterComponent } from './_components/welcome/register/index';
import { HomeComponent } from './_components/home/index';
import { TopicComponent } from './_components/home/topic/index';
import { LessonComponent } from './_components/home/topic/lesson/index';
import { AuthGuard } from './_guards/index';
import { ProfileComponent } from './_components/profile/profile.component';

const appRoutes: Routes = [
    { path: 'welcome', component: WelcomeComponent },
    { path: 'login', component: LoginComponent },
    { path: 'register', component: RegisterComponent },
    { path: 'forgot-password', component: ForgotPasswordComponent },
    { path: 'profile', component: ProfileComponent, canActivate: [AuthGuard] },
    { path: '', component: HomeComponent, canActivate: [AuthGuard] },
    { path: 'topic/:id', component: TopicComponent, canActivate: [AuthGuard] },
    { path: 'topic/:topic_id/lesson/:lesson_id', component: LessonComponent,
    	canActivate: [AuthGuard] },

    // otherwise redirect to welcome
    { path: '**', redirectTo: 'welcome' }
];

export const routing = RouterModule.forRoot(appRoutes);