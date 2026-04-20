import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ApiService } from '../core/api.service';
import { ProgressSummary } from '../core/api.types';

@Component({
  selector: 'app-progress',
  standalone: true,
  imports: [CommonModule, RouterLink],
  template: `
    <section class="page">
      <div class="card">
        <h2>Progress Dashboard</h2>
        <p class="hint">Track your session trends and topic-level mastery.</p>
      </div>

      <p *ngIf="error" class="error">{{ error }}</p>

      <ng-container *ngIf="progress">
        <div class="card">
          <p><strong>User:</strong> {{ progress.user.name }} (Session {{ progress.user.session_number }})</p>
          <div class="stats">
            <div><strong>{{ progress.stats.total_sessions }}</strong><span>Total Sessions</span></div>
            <div><strong>{{ progress.stats.average_score }}%</strong><span>Average Score</span></div>
            <div><strong>{{ progress.stats.best_score }}%</strong><span>Best Score</span></div>
            <div><strong>{{ progress.stats.total_questions_answered }}</strong><span>Total Answered</span></div>
          </div>
        </div>

        <div class="card">
          <h3>Topic Mastery</h3>
          <table>
          <thead>
            <tr><th>Topic</th><th>Category</th><th>Mastery</th><th>Attempts</th></tr>
          </thead>
          <tbody>
            <tr *ngFor="let row of progress.topic_progress">
              <td>{{ row.topic_name }}</td>
              <td>{{ row.major_category }} / {{ row.middle_category }}</td>
              <td><span [class]="masteryClass(row.mastery_percent)">{{ row.mastery_percent }}%</span></td>
              <td>{{ row.attempts_count }}</td>
            </tr>
          </tbody>
          </table>
        </div>
      </ng-container>

      <p class="card"><a routerLink="/">Back to Home</a></p>
    </section>
  `,
  styles: [`
    .page { display: grid; gap: 1rem; }
    .hint { color: var(--muted-text); }
    .error { color: var(--error-text); }
    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem; margin-top: 0.7rem; }
    .stats div { background: var(--option-bg); border: 1px solid var(--border-color); border-radius: 10px; padding: 0.7rem; display: grid; gap: 0.2rem; }
    .stats span { color: var(--muted-text); font-size: 0.86rem; }
    .good { color: var(--success-text); font-weight: 700; }
    .mid { color: var(--warning-text); font-weight: 700; }
    .low { color: var(--error-text); font-weight: 700; }
    table { width: 100%; border-collapse: collapse; margin: 0.75rem 0; }
    th, td { border: 1px solid var(--border-color); padding: 0.4rem; text-align: left; }
  `]
})
export class ProgressComponent implements OnInit {
  progress: ProgressSummary | null = null;
  error = '';

  constructor(
    private readonly route: ActivatedRoute,
    private readonly api: ApiService
  ) {}

  ngOnInit(): void {
    const routeUserId = Number(this.route.snapshot.paramMap.get('userId'));
    const userId = routeUserId > 0 ? routeUserId : this.api.getLearnerUserId();
    if (userId > 0) {
      this.api.setLearnerUserId(userId);
    } else {
      this.error = 'No learner user ID found. Set it on Home first.';
      return;
    }

    this.api.getProgress(userId).subscribe({
      next: (summary) => {
        this.progress = summary;
      },
      error: () => {
        this.error = 'Failed to load progress.';
      },
    });
  }

  masteryClass(value: number): string {
    if (value >= 80) {
      return 'good';
    }
    if (value >= 50) {
      return 'mid';
    }
    return 'low';
  }
}
