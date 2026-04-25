import { CommonModule } from '@angular/common';
import { Component, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ApiService } from '../core/api.service';
import { DetailedSession } from '../core/api.types';
import { LearnerService } from '../core/learner.service';

@Component({
  selector: 'app-review',
  standalone: true,
  imports: [CommonModule, RouterLink],
  template: `
    <section class="review-page">
      <div *ngIf="error()" class="error-container card">
        <p class="error">{{ error() }}</p>
        <a routerLink="/" class="btn btn-secondary">Return Home</a>
      </div>

      <div *ngIf="!review() && !error()" class="loading-container">
        <div class="spinner"></div>
        <p>Loading session details...</p>
      </div>

      <div *ngIf="review() as data" class="review-container">
        <header class="review-header-meta">
          <div class="header-left">
            <a [routerLink]="['/progress', learnerService.learner()?.id]" class="back-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
              Back to Progress
            </a>
            <h1>Review: {{ data.exam.title }}</h1>
          </div>
          <div class="review-summary-pill pill" *ngIf="data.session" [class.success]="data.session.score >= 80" [class.error]="data.session.score < 80">
            {{ data.session.correct_answers }}/{{ data.session.total_questions }} ({{ data.session.score }}%)
          </div>
        </header>

        <div class="review-list">
          <article *ngFor="let q of data.exam.questions; let i = index" class="card review-card" [class.wrong]="!q.is_correct">
            <div class="review-q-header">
              <span class="q-num">#{{ i + 1 }}</span>
              <span class="topic-tag">{{ q.topic_name }}</span>
              <span class="difficulty-tag" [class]="q.difficulty">{{ q.difficulty }}</span>
              <span class="pill" [class.success]="q.is_correct" [class.error]="!q.is_correct">
                {{ q.is_correct ? 'Correct' : 'Incorrect' }}
              </span>
            </div>
            
            <p class="question-text">{{ q.question_text }}</p>

            <div class="review-options">
              <div *ngFor="let opt of q.options" 
                   class="review-option-item"
                   [class.correct-answer]="opt.is_correct"
                   [class.user-wrong]="q.selected_option_id === opt.id && !opt.is_correct">
                <span class="marker" *ngIf="opt.is_correct">✓</span>
                <span class="marker" *ngIf="q.selected_option_id === opt.id && !opt.is_correct">✗</span>
                {{ opt.option_text }}
              </div>
            </div>

            <div class="review-explanation card" *ngIf="q.explanation">
              <strong>Explanation:</strong>
              <p>{{ q.explanation }}</p>
            </div>
          </article>
        </div>

        <footer class="review-footer">
          <a routerLink="/" class="btn btn-primary">Back to Home</a>
          <a [routerLink]="['/progress', learnerService.learner()?.id]" class="btn btn-secondary">My Dashboard</a>
        </footer>
      </div>
    </section>
  `,
  styles: [`
    .review-page { max-width: 800px; margin: 0 auto; padding-bottom: 4rem; }
    
    .review-header-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .header-left { display: flex; align-items: center; gap: 1.5rem; }
    .header-left h1 { font-size: 1.5rem; margin: 0; }
    .back-link { display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 0.9rem; }
    .back-link:hover { color: var(--text-main); }

    .review-summary-pill { font-weight: 700; }
    .review-summary-pill.success { background: var(--success-bg); color: var(--success-text); }
    .review-summary-pill.error { background: rgba(239, 68, 68, 0.1); color: var(--error-text); }

    .review-list { display: grid; gap: 1.5rem; margin-bottom: 3rem; }
    .review-card { padding: 2rem; }
    .review-card.wrong { border-left: 4px solid var(--error-text); }
    
    .review-q-header { display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; }
    .q-num { font-weight: 800; color: var(--accent-primary); }
    .topic-tag { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); }
    
    .difficulty-tag { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; border-radius: 4px; padding: 1px 6px; }
    .difficulty-tag.easy { color: var(--success-text); background: var(--success-bg); }
    .difficulty-tag.medium { color: var(--warning-text); background: rgba(245, 158, 11, 0.1); }
    .difficulty-tag.hard { color: var(--error-text); background: rgba(239, 68, 68, 0.1); }

    .pill.success { background: var(--success-bg); color: var(--success-text); }
    .pill.error { background: rgba(239, 68, 68, 0.1); color: var(--error-text); }

    .question-text { font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-main); line-height: 1.5; }
    
    .review-options { display: grid; gap: 0.5rem; margin: 1.5rem 0; }
    .review-option-item { padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; position: relative; }
    .review-option-item.correct-answer { border-color: var(--success-text); background: var(--success-bg); color: var(--success-text); font-weight: 700; }
    .review-option-item.user-wrong { border-color: var(--error-text); background: rgba(239, 68, 68, 0.05); color: var(--error-text); }
    
    .marker { font-weight: 800; font-size: 1.1rem; }

    .review-explanation { background: var(--bg-color); border: none; font-size: 0.9rem; color: var(--text-muted); margin-top: 1.5rem; padding: 1.25rem; }
    .review-explanation strong { display: block; margin-bottom: 0.5rem; color: var(--text-main); }
    .review-explanation p { margin: 0; line-height: 1.6; }

    .review-footer { display: flex; justify-content: center; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 2rem; }

    .error-container { text-align: center; padding: 3rem; }
    .loading-container { text-align: center; padding: 5rem 0; color: var(--text-muted); }
    .spinner { width: 40px; height: 40px; border: 3px solid var(--border-color); border-top-color: var(--accent-primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1.5rem; }
    @keyframes spin { to { transform: rotate(360deg); } }
  `]
})
export class ReviewComponent implements OnInit {
  review = signal<DetailedSession | null>(null);
  error = signal<string>('');

  constructor(
    private readonly route: ActivatedRoute,
    private readonly api: ApiService,
    public readonly learnerService: LearnerService
  ) {}

  ngOnInit(): void {
    const sessionId = Number(this.route.snapshot.paramMap.get('sessionId'));
    if (sessionId) {
      this.loadReview(sessionId);
    } else {
      this.error.set('Invalid session reference.');
    }
  }

  loadReview(sessionId: number): void {
    this.api.getDetailedSession(sessionId).subscribe({
      next: (data) => this.review.set(data),
      error: (err) => this.error.set(err.error?.message || 'Failed to load detailed review.')
    });
  }
}
