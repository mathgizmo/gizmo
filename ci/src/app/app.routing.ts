import { Routes, RouterModule } from '@angular/router';

import { WelcomeComponent } from './welcome/index';
import { LoginComponent } from './login/index';
import { RegisterComponent } from './register/index';
import { HomeComponent } from './home/index';
import { TopicComponent } from './topic/index';
import { LessonComponent } from './lesson/index';
import { AuthGuard } from './_guards/index';
import { ProfileComponent } from './profile/profile.component';

const appRoutes: Routes = [
    { path: 'welcome', component: WelcomeComponent },
    { path: 'login', component: LoginComponent },
    { path: 'register', component: RegisterComponent },
    { path: 'profile', component: ProfileComponent },
    { path: '', component: HomeComponent, canActivate: [AuthGuard] },
    { path: 'topic/:id', component: TopicComponent, canActivate: [AuthGuard] },
    { path: 'topic/:topic_id/lesson/:lesson_id', component: LessonComponent, canActivate: [AuthGuard] },

    // otherwise redirect to welcome
    { path: '**', redirectTo: 'welcome' }
];

export const routing = RouterModule.forRoot(appRoutes);