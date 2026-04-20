import { Routes } from '@angular/router';
import { ExamSessionComponent } from './pages/exam-session.component';
import { HomeComponent } from './pages/home.component';
import { ProgressComponent } from './pages/progress.component';

export const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'take-test/:examId', component: ExamSessionComponent },
  { path: 'progress/:userId', component: ProgressComponent },
];
