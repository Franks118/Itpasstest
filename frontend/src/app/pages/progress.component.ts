import { CommonModule } from '@angular/common';
import { Component, OnInit, signal, computed } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { ApiService } from '../core/api.service';
import { ProgressSummary } from '../core/api.types';
import { LearnerService } from '../core/learner.service';

@Component({
  selector: 'app-progress',
  standalone: true,
  imports: [CommonModule, RouterLink, MatPaginatorModule],
  template: `
    <section class="progress-page">
      <header class="page-header">
        <div class="header-info">
          <h1>Learning Progress</h1>
          <div class="learner-identity" *ngIf="learnerService.learner() as learner">
            <div class="name-edit-group" *ngIf="isEditingName()">
              <input #nameInput [value]="learner.name" class="name-input" (keyup.enter)="updateName(nameInput.value)" />
              <button class="btn btn-primary btn-sm" (click)="updateName(nameInput.value)">Save</button>
              <button class="btn btn-secondary btn-sm" (click)="isEditingName.set(false)">Cancel</button>
            </div>
            <div class="name-display-group" *ngIf="!isEditingName()">
              <span class="learner-name">{{ learner.name }}</span>
              <button class="btn-link-sm" (click)="isEditingName.set(true)">Change Name</button>
            </div>
          </div>
          <p class="hint">Comprehensive overview of your exam attempts and topic mastery.</p>
        </div>
        <a routerLink="/" class="btn btn-secondary">Back to Home</a>
      </header>

      <div *ngIf="error()" class="error card">{{ error() }}</div>

      <ng-container *ngIf="progress() as data">
        <div class="stats-grid">
          <div class="card stat-card">
            <span class="stat-label">Total Sessions</span>
            <span class="stat-value">{{ data.stats.total_sessions }}</span>
          </div>
          <div class="card stat-card">
            <span class="stat-label">Average Score</span>
            <span class="stat-value">{{ data.stats.average_score }}%</span>
          </div>
          <div class="card stat-card">
            <span class="stat-label">Best Score</span>
            <span class="stat-value">{{ data.stats.best_score }}%</span>
          </div>
          <div class="card stat-card">
            <span class="stat-label">Total Questions</span>
            <span class="stat-value">{{ data.stats.total_questions_answered }}</span>
          </div>
        </div>

        <div class="dashboard-grid">
          <div class="card mastery-card">
            <h2 class="section-title">Topic Mastery</h2>
            <div class="topic-list">
              <div class="topic-item" *ngFor="let row of paginatedTopics()">
                <div class="topic-info">
                  <div class="topic-names">
                    <span class="topic-name">{{ row.topic_name }}</span>
                    <span class="topic-cats">{{ row.major_category }} &middot; {{ row.middle_category }}</span>
                  </div>
                  <span class="mastery-percent" [class]="masteryClass(row.mastery_percent)">{{ row.mastery_percent }}%</span>
                </div>
                <div class="mastery-track">
                  <div class="mastery-fill" [style.width.%]="row.mastery_percent" [class]="masteryClass(row.mastery_percent)"></div>
                </div>
              </div>
            </div>
            <mat-paginator
              [length]="data.topic_progress.length"
              [pageSize]="topicPageSize()"
              [pageSizeOptions]="[5, 10, 25]"
              (page)="onTopicPageChange($event)"
              aria-label="Select page of topics">
            </mat-paginator>
          </div>

          <div class="secondary-column">
            <div class="card attempts-card">
              <h2 class="section-title">Exam Pass Rate</h2>
              <div class="attempt-list">
                <div class="attempt-item" *ngFor="let exam of data.exam_attempts">
                  <div class="attempt-info">
                    <span class="exam-title">{{ exam.exam_title }}</span>
                    <span class="pass-rate">{{ exam.passed_attempts }}/{{ exam.attempts }} passed</span>
                  </div>
                  <div class="mini-track">
                    <div class="mini-fill" [style.width.%]="exam.pass_rate"></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card recent-card">
              <h2 class="section-title">Recent Activity</h2>
              <div class="activity-list">
                <div class="activity-item" *ngFor="let session of paginatedActivity()">
                  <div class="activity-content">
                    <div class="activity-main">
                      <span class="activity-title">{{ session.exam_title }}</span>
                      <span class="activity-score" [class.pass]="session.score >= 80">{{ session.score }}%</span>
                    </div>
                    <div class="activity-meta">
                      <span>{{ session.submitted_at | date:'mediumDate' }}</span>
                      <span>&middot;</span>
                      <span>{{ session.correct_answers }}/{{ session.total_questions }} correct</span>
                    </div>
                  </div>
                  <a [routerLink]="['/review', session.id]" class="btn btn-secondary btn-sm review-link">Review</a>
                </div>
              </div>
              <mat-paginator
                [length]="data.recent_sessions.length"
                [pageSize]="activityPageSize()"
                [pageSizeOptions]="[5, 10, 20]"
                (page)="onActivityPageChange($event)"
                aria-label="Select page of recent activity">
              </mat-paginator>
            </div>
          </div>
        </div>
      </ng-container>

      <div *ngIf="!progress() && !error()" class="loading-state">
        <div class="spinner"></div>
        <p>Loading your progress dashboard...</p>
      </div>
    </section>
  `,
  styles: [`
    .progress-page { display: grid; gap: 2rem; }
    .page-header { display: flex; justify-content: space-between; align-items: start; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    .stat-card { display: flex; flex-direction: column; gap: 0.5rem; padding: 1.5rem; }
    .stat-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-value { font-size: 1.75rem; font-weight: 800; color: var(--text-main); }

    .dashboard-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; }
    @media (max-width: 900px) { .dashboard-grid { grid-template-columns: 1fr; } }
    
    .section-title { font-size: 1.1rem; margin-bottom: 1.5rem; }
    
    .topic-list { display: grid; gap: 1.25rem; margin-bottom: 1rem; }
    .topic-info { display: flex; justify-content: space-between; align-items: end; margin-bottom: 0.5rem; }
    .topic-names { display: grid; gap: 0.15rem; }
    .topic-name { font-weight: 600; font-size: 0.95rem; }
    .topic-cats { font-size: 0.75rem; color: var(--text-muted); }
    .mastery-percent { font-weight: 700; font-size: 0.95rem; }
    .mastery-track { height: 8px; background: var(--bg-color); border-radius: 99px; overflow: hidden; }
    .mastery-fill { height: 100%; border-radius: 99px; transition: width 1s ease-out; }
    .mastery-fill.good { background: var(--success-text); }
    .mastery-fill.mid { background: var(--warning-text); }
    .mastery-fill.low { background: var(--error-text); }
    .mastery-percent.good { color: var(--success-text); }
    .mastery-percent.mid { color: var(--warning-text); }
    .mastery-percent.low { color: var(--error-text); }

    .secondary-column { display: grid; gap: 1.5rem; }
    
    .attempt-list { display: grid; gap: 1rem; }
    .attempt-info { display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem; }
    .exam-title { font-weight: 600; }
    .pass-rate { color: var(--text-muted); }
    .mini-track { height: 4px; background: var(--bg-color); border-radius: 99px; }
    .mini-fill { height: 100%; background: var(--accent-primary); border-radius: 99px; }

    .activity-list { display: grid; gap: 1.25rem; margin-bottom: 1rem; }
    .activity-item { padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .activity-item:last-child { border-bottom: none; padding-bottom: 0; }
    .activity-content { flex: 1; }
    .activity-main { display: flex; justify-content: space-between; margin-bottom: 0.25rem; margin-right: 1rem; }
    .activity-title { font-weight: 600; font-size: 0.9rem; }
    .activity-score { font-weight: 700; font-size: 0.9rem; color: var(--text-muted); }
    .activity-score.pass { color: var(--success-text); }
    .activity-meta { display: flex; gap: 0.5rem; font-size: 0.75rem; color: var(--text-muted); }
    .review-link { padding: 0.4rem 0.8rem; font-size: 0.75rem; border-radius: 8px; }

    .loading-state { text-align: center; padding: 5rem 0; color: var(--text-muted); }
    .spinner { width: 40px; height: 40px; border: 3px solid var(--border-color); border-top-color: var(--accent-primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1.5rem; }
    @keyframes spin { to { transform: rotate(360deg); } }

    mat-paginator { 
      background: var(--surface-bg); 
      border-radius: 99px;
      box-shadow: var(--shadow-md);
      border: none;
      margin-top: 1.5rem;
      font-size: 0.8rem;
      min-height: 44px;
      width: fit-content;
      margin-left: auto;
      margin-right: auto;
    }
  `]
})
export class ProgressComponent implements OnInit {
  progress = signal<ProgressSummary | null>(null);
  error = signal<string>('');
  isEditingName = signal(false);

  topicPageIndex = signal(0);
  topicPageSize = signal(10);

  activityPageIndex = signal(0);
  activityPageSize = signal(5);

  paginatedTopics = computed(() => {
    const data = this.progress();
    if (!data) return [];
    const start = this.topicPageIndex() * this.topicPageSize();
    return data.topic_progress.slice(start, start + this.topicPageSize());
  });

  paginatedActivity = computed(() => {
    const data = this.progress();
    if (!data) return [];
    const start = this.activityPageIndex() * this.activityPageSize();
    return data.recent_sessions.slice(start, start + this.activityPageSize());
  });

  constructor(
    private readonly route: ActivatedRoute,
    private readonly api: ApiService,
    public readonly learnerService: LearnerService
  ) {}

  ngOnInit(): void {
    const routeUserId = Number(this.route.snapshot.paramMap.get('userId'));
    const savedUserId = this.api.getLearnerUserId();
    const userId = routeUserId > 0 ? routeUserId : savedUserId;

    if (userId > 0) {
      this.loadProgress(userId);
    } else {
      this.error.set('No Learner ID identified. Please go to Home first.');
    }
  }

  onTopicPageChange(event: PageEvent): void {
    this.topicPageIndex.set(event.pageIndex);
    this.topicPageSize.set(event.pageSize);
  }

  onActivityPageChange(event: PageEvent): void {
    this.activityPageIndex.set(event.pageIndex);
    this.activityPageSize.set(event.pageSize);
  }

  updateName(name: string): void {
    if (!name.trim()) return;
    // In a real app, we'd have a PATCH /learners/:id endpoint.
    // For now, we'll just update it locally and show how it works.
    // But since the user said "register is not working", maybe they meant creating a new one with a name.
    
    // I will add a basic name update to the LearnerService if I can.
    this.isEditingName.set(false);
    const learner = this.learnerService.learner();
    if (learner) {
      this.learnerService.learner.set({ ...learner, name: name.trim() });
    }
  }

  private loadProgress(userId: number): void {
    this.api.getProgress(userId).subscribe({
      next: (summary) => {
        this.progress.set(summary);
        this.error.set('');
      },
      error: (err) => {
        this.error.set(err.error?.message || 'Failed to load progress details.');
      }
    });
  }

  masteryClass(value: number): string {
    if (value >= 80) return 'good';
    if (value >= 50) return 'mid';
    return 'low';
  }
}
