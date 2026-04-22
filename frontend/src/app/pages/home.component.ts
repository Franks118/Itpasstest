import { CommonModule } from '@angular/common';
import { Component, OnInit, effect, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { ApiService } from '../core/api.service';
import { Exam, InProgressSession } from '../core/api.types';
import { LearnerService } from '../core/learner.service';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  template: `
    <section class="home-page">
      <div class="welcome-card card">
        <h1>Prepare for IT Passport</h1>
        <p class="hint">Master the syllabus with prebuilt tests from official notes. No login needed—your progress follows your ID.</p>
        <div class="welcome-actions">
          <a routerLink="/progress/{{ learnerService.learner()?.id }}" class="btn btn-secondary" *ngIf="learnerService.learner()">
            View Dashboard
          </a>
        </div>
      </div>

      <div *ngIf="inProgressSessions().length > 0" class="resume-hub">
        <h2 class="section-title">Resume In-Progress</h2>
        <div class="resume-grid">
          <article class="card resume-card" *ngFor="let session of inProgressSessions()">
            <div class="resume-info">
              <h3>{{ session.exam.title }}</h3>
              <p class="hint">Last active: {{ session.updated_at | date:'short' }}</p>
              <div class="resume-progress">
                <div class="resume-progress-bar">
                  <div class="resume-progress-fill" [style.width.%]="((session.current_question_index + 1) / session.total_questions) * 100"></div>
                </div>
                <span class="resume-progress-text">Q{{ session.current_question_index + 1 }} / {{ session.total_questions }}</span>
              </div>
            </div>
            <a [routerLink]="['/take-test', session.exam_id]" class="btn btn-primary">Resume</a>
          </article>
        </div>
      </div>

      <div class="exam-hub">
        <div class="hub-header">
          <h2 class="section-title">Available Tests</h2>
          <div class="mode-tabs">
            <button *ngFor="let mode of modes" 
                    [class.active]="selectedMode() === mode.id" 
                    (click)="selectedMode.set(mode.id)">
              {{ mode.label }}
            </button>
          </div>
        </div>

        <p *ngIf="error()" class="error card">{{ error() }}</p>

        <div class="exam-grid" *ngIf="filteredExams.length > 0">
          <article class="card exam-card" *ngFor="let exam of filteredExams">
            <div class="exam-header">
              <h3>{{ exam.title }}</h3>
              <span class="pill">{{ modeLabel(exam.title) }}</span>
            </div>
            <p class="exam-desc">{{ exam.description || 'Practice test derived from official syllabus.' }}</p>
            <div class="exam-meta">
              <div class="meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                {{ exam.total_questions }} Questions
              </div>
              <div class="meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                {{ exam.duration_minutes }} Mins
              </div>
            </div>
            <a [routerLink]="['/take-test', exam.id]" class="btn btn-secondary start-btn">Start Test</a>
          </article>
        </div>

        <div *ngIf="filteredExams.length === 0" class="empty-state card">
          <p>Loading tests or no tests match the selected mode.</p>
        </div>
      </div>
    </section>
  `,
  styles: [`
    .home-page { display: grid; gap: 2.5rem; }
    .welcome-card { border: none; background: linear-gradient(135deg, var(--accent-soft) 0%, var(--surface-bg) 100%); padding: 2.5rem; }
    .welcome-card h1 { font-size: 2.25rem; margin-bottom: 0.75rem; }
    .welcome-card .hint { font-size: 1.1rem; max-width: 600px; margin-bottom: 1.5rem; }
    
    .section-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--text-main); }
    
    .resume-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
    .resume-card { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; }
    .resume-info h3 { font-size: 1rem; margin-bottom: 0.25rem; }
    .resume-progress { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; }
    .resume-progress-bar { flex: 1; height: 6px; background: var(--bg-color); border-radius: 99px; overflow: hidden; width: 100px; }
    .resume-progress-fill { height: 100%; background: var(--accent-primary); }
    .resume-progress-text { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }

    .hub-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .mode-tabs { display: flex; background: var(--surface-bg); border: 1px solid var(--border-color); padding: 0.25rem; border-radius: 10px; }
    .mode-tabs button { background: transparent; border: none; padding: 0.5rem 1rem; font-size: 0.875rem; color: var(--text-muted); border-radius: 8px; }
    .mode-tabs button.active { background: var(--accent-soft); color: var(--accent-primary); }

    .exam-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.25rem; }
    .exam-card { display: flex; flex-direction: column; height: 100%; }
    .exam-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem; }
    .exam-header h3 { font-size: 1.1rem; flex: 1; }
    .exam-desc { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; flex: 1; }
    .exam-meta { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
    .meta-item { display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }
    .start-btn { text-align: center; text-decoration: none; }
  `]
})
export class HomeComponent implements OnInit {
  exams = signal<Exam[]>([]);
  inProgressSessions = signal<InProgressSession[]>([]);
  selectedMode = signal<'quick' | 'long' | 'shuffle' | 'full' | 'mastery'>('quick');
  error = signal<string>('');
  
  modes: Array<{ id: 'quick' | 'long' | 'shuffle' | 'full' | 'mastery', label: string }> = [
    { id: 'quick', label: 'Quick Recap' },
    { id: 'long', label: 'Long Quest' },
    { id: 'shuffle', label: 'Shuffle' },
    { id: 'full', label: 'Full PDF' },
    { id: 'mastery', label: 'Domain Mastery' }
  ];

  constructor(
    private readonly api: ApiService,
    public readonly learnerService: LearnerService
  ) {
    effect(() => {
      const learner = this.learnerService.learner();
      if (learner) {
        this.loadInProgress(learner.id);
      }
    });
  }

  ngOnInit(): void {
    this.api.getExams().subscribe({
      next: (data) => this.exams.set(data),
      error: () => this.error.set('Failed to load exams. Ensure the backend is running.')
    });
  }

  loadInProgress(userId: number): void {
    this.api.getInProgressSessions(userId).subscribe({
      next: (sessions) => this.inProgressSessions.set(sessions),
      error: (err) => console.error('Failed to load in-progress sessions', err)
    });
  }

  modeLabel(title: string): string {
    if (title.includes('Quick')) return 'Quick';
    if (title.includes('Long')) return 'Long';
    if (title.includes('Shuffle')) return 'Shuffle';
    if (title.includes('Mastery')) return 'Mastery';
    return 'Full';
  }

  get filteredExams(): Exam[] {
    const currentMode = this.selectedMode();
    return this.exams().filter((exam) => {
      const t = exam.title;
      if (currentMode === 'quick') return t.includes('Quick');
      if (currentMode === 'long') return t.includes('Long');
      if (currentMode === 'shuffle') return t.includes('Shuffle');
      if (currentMode === 'mastery') return t.includes('Mastery');
      return t.includes('Full');
    });
  }
}
