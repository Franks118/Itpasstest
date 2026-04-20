import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ApiService } from '../core/api.service';
import { SessionResult, StartSessionResponse } from '../core/api.types';

@Component({
  selector: 'app-exam-session',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  template: `
    <section class="page">
      <div class="card">
        <h2>Take Test</h2>
        <p class="hint">Answer each question and submit to update your progress dashboard.</p>
      </div>

      <div *ngIf="!loaded" class="card start">
        <label>User ID <input class="input" [(ngModel)]="userId" (ngModelChange)="onUserIdChange($event)" type="number" min="1" /></label>
        <button class="btn btn-primary" (click)="start()">Start Exam</button>
      </div>

      <p *ngIf="error" class="error">{{ error }}</p>

      <div *ngIf="session" class="card exam">
        <div class="row">
          <h3>{{ session.exam.title }}</h3>
          <span class="pill">Question {{ currentIndex + 1 }} / {{ session.exam.questions.length }}</span>
        </div>
        <p class="hint">{{ session.exam.description }}</p>
        <div class="progress-track">
          <div class="progress-fill" [style.width.%]="((currentIndex + 1) / session.exam.questions.length) * 100"></div>
        </div>

        <div *ngIf="currentQuestion" class="question">
          <p><strong>Q{{ currentIndex + 1 }}.</strong> {{ currentQuestion.question_text }}</p>
          <label *ngFor="let option of currentQuestion.options" class="option">
            <input
              type="radio"
              [name]="'q-' + currentQuestion.id"
              [value]="option.id"
              [checked]="answers[currentQuestion.id!] === option.id"
              (change)="pick(currentQuestion.id!, option.id ?? null)"
            />
            {{ option.option_text }}
          </label>
        </div>

        <p class="actions">
          <button class="btn btn-secondary" (click)="prev()" [disabled]="currentIndex === 0">Previous</button>
          <button class="btn btn-secondary" (click)="next()" [disabled]="currentIndex === session.exam.questions.length - 1">Next</button>
          <button
            *ngIf="currentIndex === session.exam.questions.length - 1"
            class="btn btn-primary"
            (click)="submit()"
            [disabled]="submitted"
          >
            Submit Exam
          </button>
        </p>
      </div>

      <div *ngIf="result" class="card result">
        <h3>Result</h3>
        <p class="score">Score: {{ result.score }}%</p>
        <p>Correct: {{ result.correct_answers }} / {{ result.total_questions }}</p>
        <p><a [routerLink]="['/progress', userId]">See Progress</a></p>
      </div>
    </section>
  `,
  styles: [`
    .page { display: grid; gap: 1rem; }
    .hint { color: var(--muted-text); }
    .start { display: flex; gap: 0.7rem; align-items: end; }
    .exam { display: grid; gap: 0.8rem; }
    .row { display: flex; justify-content: space-between; gap: 0.65rem; align-items: center; }
    .progress-track { width: 100%; height: 10px; background: var(--track-bg); border-radius: 999px; overflow: hidden; }
    .progress-fill { height: 100%; background: var(--accent-strong); }
    .question { display: grid; gap: 0.6rem; }
    .option { border: 1px solid var(--border-color); border-radius: 10px; padding: 0.6rem 0.75rem; display: flex; gap: 0.55rem; align-items: center; background: var(--option-bg); }
    .actions { display: flex; gap: 0.6rem; flex-wrap: wrap; }
    .error { color: var(--error-text); }
    .result { border-color: var(--success-border); background: var(--success-bg); }
    .score { font-size: 1.1rem; font-weight: 700; color: var(--success-text); }
  `]
})
export class ExamSessionComponent implements OnInit {
  examId = 0;
  userId = 0;
  session: StartSessionResponse | null = null;
  result: SessionResult | null = null;
  error = '';
  loaded = false;
  currentIndex = 0;
  submitted = false;
  answers: Record<number, number | null> = {};

  constructor(
    private readonly route: ActivatedRoute,
    private readonly api: ApiService
  ) {}

  get currentQuestion() {
    return this.session?.exam.questions[this.currentIndex];
  }

  ngOnInit(): void {
    this.examId = Number(this.route.snapshot.paramMap.get('examId'));
    const routeUserId = Number(this.route.snapshot.queryParamMap.get('userId') ?? 0);
    this.userId = routeUserId > 0 ? routeUserId : this.api.getLearnerUserId();
    if (this.userId > 0) {
      this.api.setLearnerUserId(this.userId);
    }

    if (this.userId > 0) {
      this.start();
    }
  }

  start(): void {
    this.error = '';
    if (!Number.isInteger(this.userId) || this.userId <= 0) {
      this.error = 'Please enter a valid Learner User ID.';
      return;
    }

    this.api.setLearnerUserId(this.userId);
    this.api.startSession(this.userId, this.examId).subscribe({
      next: (session) => {
        this.session = session;
        this.loaded = true;
      },
      error: () => {
        this.error = 'Unable to start exam. Ensure user and exam exist.';
      },
    });
  }

  onUserIdChange(value: number): void {
    const parsed = Number(value);
    if (Number.isInteger(parsed) && parsed > 0) {
      this.api.setLearnerUserId(parsed);
    }
  }

  pick(questionId: number, optionId: number | null): void {
    this.answers[questionId] = optionId;
  }

  prev(): void {
    this.currentIndex = Math.max(0, this.currentIndex - 1);
  }

  next(): void {
    if (!this.session) {
      return;
    }
    this.currentIndex = Math.min(this.session.exam.questions.length - 1, this.currentIndex + 1);
  }

  submit(): void {
    if (!this.session || this.submitted) {
      return;
    }

    const payload = this.session.exam.questions
      .filter((q) => typeof q.id === 'number')
      .map((q) => ({
        question_id: q.id as number,
        selected_option_id: this.answers[q.id as number] ?? null,
      }));

    this.api.submitSession(this.session.session_id, payload).subscribe({
      next: (result) => {
        this.result = result;
        this.submitted = true;
      },
      error: () => {
        this.error = 'Failed to submit exam.';
      },
    });
  }
}
