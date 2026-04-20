import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { ApiService } from '../core/api.service';
import { Exam } from '../core/api.types';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  template: `
    <section class="page">
      <div class="card intro">
        <h2>Choose a Premade IT Passport Test</h2>
        <p class="hint">Tests are prebuilt from the IT Passport syllabus PDF. Pick your mode below.</p>
        <label>
          Learner User ID
          <input class="input" [(ngModel)]="userId" (ngModelChange)="onUserIdChange($event)" type="number" min="1" />
        </label>
        <div class="mode-grid">
          <button class="mode card" [class.active]="selectedMode === 'quick'" (click)="selectedMode = 'quick'" type="button">
            <h3>Quick Recap</h3>
            <p>Short review set for fast revision before studying sessions.</p>
          </button>
          <button class="mode card" [class.active]="selectedMode === 'long'" (click)="selectedMode = 'long'" type="button">
            <h3>Long Quest</h3>
            <p>Longer full-coverage test across strategy, management, and technology topics.</p>
          </button>
          <button class="mode card" [class.active]="selectedMode === 'shuffle'" (click)="selectedMode = 'shuffle'" type="button">
            <h3>Shuffle Drill</h3>
            <p>Questions appear in randomized order every attempt.</p>
          </button>
          <button class="mode card" [class.active]="selectedMode === 'full'" (click)="selectedMode = 'full'" type="button">
            <h3>Full PDF Coverage</h3>
            <p>Comprehensive test using the full syllabus-derived question set.</p>
          </button>
        </div>
        <a *ngIf="userId > 0" [routerLink]="['/progress', userId]">View My Progress</a>
      </div>

      <p *ngIf="error" class="error">{{ error }}</p>

      <div class="grid" *ngIf="filteredExams.length > 0">
        <article class="card exam-card" *ngFor="let exam of filteredExams">
          <div class="row">
            <h3>{{ exam.title }}</h3>
            <span class="pill" *ngIf="exam.title.includes('Quick Recap')">Quick Recap</span>
            <span class="pill" *ngIf="exam.title.includes('Long Quest')">Long Quest</span>
            <span class="pill" *ngIf="exam.title.includes('Shuffle')">Shuffle</span>
            <span class="pill" *ngIf="exam.title.includes('Full PDF')">Full PDF</span>
          </div>
          <p>{{ exam.description || 'No description provided.' }}</p>
          <div class="meta">
            <span>{{ exam.questions_count ?? exam.total_questions }} questions</span>
            <span>{{ exam.duration_minutes }} mins</span>
          </div>
          <a [routerLink]="['/take-test', exam.id]" [queryParams]="{ userId: userId || null }">Start Test</a>
        </article>
      </div>

      <p *ngIf="filteredExams.length === 0" class="card">No premade tests found yet. Run backend seeder to load them.</p>
    </section>
  `,
  styles: [`
    .page { display: grid; gap: 1rem; }
    .intro { display: grid; gap: 0.7rem; }
    .hint { color: var(--muted-text); }
    .error { color: var(--error-text); }
    .mode-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0.7rem; }
    .mode { text-align: left; cursor: pointer; border: 1px solid var(--border-color); background: var(--surface-bg); color: var(--text-color); }
    .mode h3 { margin-bottom: 0.35rem; }
    .mode p { color: var(--muted-text); }
    .mode.active { border-color: var(--accent-strong); box-shadow: 0 0 0 2px var(--accent-soft); }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 0.9rem; }
    .exam-card { display: grid; gap: 0.65rem; }
    .row { display: flex; justify-content: space-between; gap: 0.5rem; align-items: center; }
    .meta { display: flex; gap: 0.65rem; color: var(--muted-text); font-size: 0.9rem; }
  `]
})
export class HomeComponent implements OnInit {
  exams: Exam[] = [];
  selectedMode: 'quick' | 'long' | 'shuffle' | 'full' = 'quick';
  userId = 0;
  error = '';

  constructor(private readonly api: ApiService) {}

  ngOnInit(): void {
    const savedLearnerId = this.api.getLearnerUserId();
    if (savedLearnerId > 0) {
      this.userId = savedLearnerId;
    }

    this.api.getExams().subscribe({
      next: (data) => {
        const premade = data.filter((exam) =>
          exam.title.includes('Quick Recap') ||
          exam.title.includes('Long Quest') ||
          exam.title.includes('Shuffle') ||
          exam.title.includes('Full PDF')
        );
        this.exams = premade.length > 0 ? premade : data;
      },
      error: () => {
        this.error = 'Failed to load exams. Make sure Laravel API is running.';
      },
    });
  }

  onUserIdChange(value: number): void {
    const parsed = Number(value);
    if (Number.isInteger(parsed) && parsed > 0) {
      this.api.setLearnerUserId(parsed);
    }
  }

  get filteredExams(): Exam[] {
    return this.exams.filter((exam) => {
      if (this.selectedMode === 'quick') {
        return exam.title.includes('Quick Recap');
      }
      if (this.selectedMode === 'long') {
        return exam.title.includes('Long Quest');
      }
      if (this.selectedMode === 'shuffle') {
        return exam.title.includes('Shuffle');
      }

      return exam.title.includes('Full PDF');
    });
  }
}
