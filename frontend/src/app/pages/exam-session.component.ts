import { CommonModule } from '@angular/common';
import { HttpErrorResponse } from '@angular/common/http';
import { Component, OnInit, effect, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ApiService } from '../core/api.service';
import { DetailedSession, RevealAnswerResponse, SessionResult, StartSessionResponse } from '../core/api.types';
import { LearnerService } from '../core/learner.service';

@Component({
  selector: 'app-exam-session',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  template: `
    <section class="exam-page">
      <!-- Active Exam Mode -->
      <div *ngIf="session() && !result() && !review()" class="exam-container">
        <header class="exam-header-meta">
          <div class="header-left">
            <a routerLink="/" class="back-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
              Exit
            </a>
            <h1>{{ session()?.exam?.title }}</h1>
          </div>
          <div class="header-right">
             <span class="pill">Question {{ currentIndex + 1 }} of {{ session()?.exam?.questions?.length }}</span>
          </div>
        </header>

        <div class="progress-bar-container">
          <div class="progress-bar-fill" [style.width.%]="((currentIndex + 1) / (session()?.exam?.questions?.length || 1)) * 100"></div>
        </div>

        <div *ngIf="currentQuestion" class="question-card card">
          <div class="question-header">
            <span class="topic-tag">{{ currentQuestion.topic_name }}</span>
            <span class="difficulty-tag" [class]="currentQuestion.difficulty">{{ currentQuestion.difficulty }}</span>
          </div>
          
          <p class="question-text">{{ currentQuestion.question_text }}</p>

          <div class="options-grid">
            <label *ngFor="let option of currentQuestion.options" 
                   class="option-item" 
                   [class.selected]="answers[currentQuestion.id!] === option.id"
                   [class.revealed-correct]="revealed[currentQuestion.id!]?.correct_option_id === option.id">
              <input
                type="radio"
                [name]="'q-' + currentQuestion.id"
                [value]="option.id"
                [checked]="answers[currentQuestion.id!] === option.id"
                (change)="pick(currentQuestion.id!, option.id ?? null)"
                [disabled]="!!revealed[currentQuestion.id!]"
              />
              <span class="option-text">{{ option.option_text }}</span>
            </label>
          </div>

          <!-- Revealed Answer Feedback -->
          <div *ngIf="revealed[currentQuestion.id!] as info" class="reveal-feedback card">
            <div class="reveal-header">
              <span class="pill success">Correct Answer Revealed</span>
            </div>
            <p class="explanation" *ngIf="info.explanation"><strong>Explanation:</strong> {{ info.explanation }}</p>
          </div>
        </div>

        <footer class="exam-footer">
          <div class="footer-left">
            <button class="btn btn-secondary" (click)="prev()" [disabled]="currentIndex === 0">Previous</button>
            <button class="btn btn-secondary reveal-btn" (click)="showAnswer()" *ngIf="currentQuestion && !revealed[currentQuestion.id!]">
              Show Answer
            </button>
          </div>
          
          <div class="footer-center">
            <span class="save-status" *ngIf="lastSaved()">Auto-saved at {{ lastSaved() | date:'shortTime' }}</span>
          </div>

          <div class="footer-right">
            <button *ngIf="currentIndex < (session()?.exam?.questions?.length || 0) - 1" 
                    class="btn btn-primary" 
                    (click)="next()">Next</button>
            
            <button *ngIf="currentIndex === (session()?.exam?.questions?.length || 0) - 1"
                    class="btn btn-primary submit-btn"
                    (click)="submit()"
                    [disabled]="submitted">
              Submit Exam
            </button>
          </div>
        </footer>
      </div>

      <!-- Simple Result Summary -->
      <div *ngIf="result() && !review()" class="result-container card">
        <div class="result-header">
          <div class="result-icon" [class.pass]="(result()?.score || 0) >= 80">
            <svg *ngIf="(result()?.score || 0) >= 80" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <svg *ngIf="(result()?.score || 0) < 80" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
          </div>
          <h2>{{ (result()?.score || 0) >= 80 ? 'Well Done!' : 'Keep Practicing' }}</h2>
          <p class="score-display">{{ result()?.score }}%</p>
        </div>

        <div class="result-stats">
          <div class="stat-box">
            <span class="stat-val">{{ result()?.correct_answers }}</span>
            <span class="stat-label">Correct</span>
          </div>
          <div class="stat-box">
            <span class="stat-val">{{ result()?.total_questions }}</span>
            <span class="stat-label">Total</span>
          </div>
        </div>

        <div class="result-actions">
          <button class="btn btn-primary" (click)="loadReview()">Review All Answers</button>
          <a routerLink="/" class="btn btn-secondary">Back to Home</a>
        </div>
      </div>

      <!-- Detailed Review Mode -->
      <div *ngIf="review() as data" class="review-container">
        <header class="review-header-meta">
          <h1>Review: {{ data.exam.title }}</h1>
          <div class="review-summary-pill pill" *ngIf="data.session">
            {{ data.session.correct_answers }}/{{ data.session.total_questions }} ({{ data.session.score }}%)
          </div>
        </header>

        <div class="review-list">
          <article *ngFor="let q of data.exam.questions; let i = index" class="card review-card" [class.wrong]="!q.is_correct">
            <div class="review-q-header">
              <span class="q-num">#{{ i + 1 }}</span>
              <span class="topic-tag">{{ q.topic_name }}</span>
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
                <span class="marker"></span>
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

      <div *ngIf="error()" class="error-container card">
        <p class="error">{{ error() }}</p>
        <a routerLink="/" class="btn btn-secondary">Return Home</a>
      </div>

      <div *ngIf="!session() && !error() && !result() && !review()" class="loading-container">
        <div class="spinner"></div>
        <p>Preparing your exam session...</p>
      </div>
    </section>
  `,
  styles: [`
    .exam-page { max-width: 800px; margin: 0 auto; }
    
    .exam-header-meta, .review-header-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .header-left { display: flex; align-items: center; gap: 1.5rem; }
    .header-left h1 { font-size: 1.25rem; }
    .back-link { display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 0.9rem; }
    .back-link:hover { color: var(--text-main); }

    .progress-bar-container { height: 8px; background: var(--border-color); border-radius: 99px; overflow: hidden; margin-bottom: 2.5rem; }
    .progress-bar-fill { height: 100%; background: var(--accent-primary); transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1); }

    .question-card { padding: 2.5rem; margin-bottom: 2rem; position: relative; }
    .question-header { display: flex; gap: 0.75rem; margin-bottom: 1.25rem; }
    .topic-tag { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); }
    .difficulty-tag { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; border-radius: 4px; padding: 1px 6px; }
    .difficulty-tag.easy { color: var(--success-text); background: var(--success-bg); }
    .difficulty-tag.medium { color: var(--warning-text); background: rgba(245, 158, 11, 0.1); }
    .difficulty-tag.hard { color: var(--error-text); background: rgba(239, 68, 68, 0.1); }
    
    .question-text { font-size: 1.25rem; font-weight: 600; margin-bottom: 2rem; color: var(--text-main); }
    
    .options-grid { display: grid; gap: 0.75rem; }
    .option-item { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; border: 1px solid var(--border-color); border-radius: 12px; cursor: pointer; transition: all 0.2s ease; }
    .option-item:hover { background: var(--surface-hover); border-color: var(--text-muted); }
    .option-item.selected { border-color: var(--accent-primary); background: var(--accent-soft); }
    .option-item.revealed-correct { border-color: var(--success-text); background: var(--success-bg); color: var(--success-text); font-weight: 700; }
    .option-item input { width: 18px; height: 18px; accent-color: var(--accent-primary); }
    .option-text { font-size: 1rem; font-weight: 500; }

    .reveal-feedback { margin-top: 2rem; background: var(--bg-color); padding: 1.25rem; border-style: dashed; }
    .reveal-header { margin-bottom: 0.75rem; }
    .pill.success { background: var(--success-bg); color: var(--success-text); }
    .explanation { font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; }

    .exam-footer { 
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: var(--surface-bg);
      padding: 1rem 2rem;
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
      z-index: 100;
      border: none;
    }
    
    .exam-container {
      padding-bottom: 100px;
    }

    .footer-left { display: flex; gap: 0.75rem; }
    .reveal-btn { color: var(--accent-primary); border-color: var(--accent-primary); }
    .reveal-btn:hover { background: var(--accent-soft); }
    .save-status { font-size: 0.75rem; color: var(--text-muted); }

    .result-container { text-align: center; padding: 4rem 2rem; }
    .result-icon { margin-bottom: 1.5rem; color: var(--error-text); }
    .result-icon.pass { color: var(--success-text); }
    .score-display { font-size: 4rem; font-weight: 800; color: var(--text-main); margin: 0.5rem 0 2rem; }
    .result-stats { display: flex; justify-content: center; gap: 3rem; margin-bottom: 3rem; }
    .stat-box { display: grid; gap: 0.25rem; }
    .stat-val { font-size: 1.5rem; font-weight: 700; }
    .stat-label { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }
    .result-actions { display: flex; justify-content: center; gap: 1rem; }

    /* Review Styles */
    .review-header-meta h1 { font-size: 1.5rem; }
    .review-list { display: grid; gap: 1.5rem; margin-bottom: 3rem; }
    .review-card { padding: 2rem; }
    .review-card.wrong { border-left: 4px solid var(--error-text); }
    .review-q-header { display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem; }
    .q-num { font-weight: 800; color: var(--accent-primary); }
    .review-options { display: grid; gap: 0.5rem; margin: 1.5rem 0; }
    .review-option-item { padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; }
    .review-option-item.correct-answer { border-color: var(--success-text); background: var(--success-bg); color: var(--success-text); font-weight: 700; }
    .review-option-item.user-wrong { border-color: var(--error-text); background: rgba(239, 68, 68, 0.05); color: var(--error-text); }
    .review-explanation { background: var(--bg-color); border: none; font-size: 0.9rem; color: var(--text-muted); margin-top: 1rem; }
    .review-footer { display: flex; justify-content: center; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 2rem; }

    .loading-container { text-align: center; padding: 5rem 0; color: var(--text-muted); }
    .spinner { width: 40px; height: 40px; border: 3px solid var(--border-color); border-top-color: var(--accent-primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1.5rem; }
    @keyframes spin { to { transform: rotate(360deg); } }
  `]
})
export class ExamSessionComponent implements OnInit {
  examId = signal<number>(0);
  session = signal<StartSessionResponse | null>(null);
  result = signal<SessionResult | null>(null);
  review = signal<DetailedSession | null>(null);
  error = signal<string>('');
  currentIndex = 0;
  submitted = false;
  answers: Record<number, number | null> = {};
  revealed: Record<number, RevealAnswerResponse | null> = {};
  lastSaved = signal<Date | null>(null);

  constructor(
    private readonly route: ActivatedRoute,
    private readonly api: ApiService,
    public readonly learnerService: LearnerService
  ) {
    effect(() => {
      const learner = this.learnerService.learner();
      const id = this.examId();
      if (learner && id && !this.session() && !this.result()) {
        this.start(learner.id, id);
      }
    });
  }

  get currentQuestion() {
    return this.session()?.exam.questions[this.currentIndex];
  }

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('examId'));
    if (id) {
      this.examId.set(id);
    } else {
      this.error.set('Invalid exam reference.');
    }
  }

  start(userId: number, examId: number): void {
    this.api.startSession(userId, examId).subscribe({
      next: (session) => {
        this.session.set(session);
        if (session.resumed && session.progress) {
          this.currentIndex = session.progress.current_question_index ?? 0;
          session.progress.answers?.forEach(ans => this.answers[ans.question_id] = ans.selected_option_id);
          this.lastSaved.set(session.progress.saved_at ? new Date(session.progress.saved_at) : null);
        }
      },
      error: (err) => this.error.set(err.error?.message || 'Failed to start exam session.')
    });
  }

  pick(questionId: number, optionId: number | null): void {
    this.answers[questionId] = optionId;
    this.syncProgress();
  }

  showAnswer(): void {
    const sess = this.session();
    const q = this.currentQuestion;
    if (!sess || !q || !q.id) return;

    this.api.revealAnswer(sess.session_id, q.id).subscribe({
      next: (res) => this.revealed[q.id!] = res,
      error: (err) => console.error('Reveal failed', err)
    });
  }

  prev(): void {
    if (this.currentIndex > 0) {
      this.currentIndex--;
      this.syncProgress();
    }
  }

  next(): void {
    const sess = this.session();
    if (sess && this.currentIndex < sess.exam.questions.length - 1) {
      this.currentIndex++;
      this.syncProgress();
    }
  }

  private syncProgress(): void {
    const learner = this.learnerService.learner();
    const sess = this.session();
    if (!sess || this.submitted || !learner) return;

    const payload = Object.entries(this.answers).map(([qid, oid]) => ({
      question_id: Number(qid),
      selected_option_id: oid
    }));

    this.api.saveProgress(sess.session_id, learner.id, this.currentIndex, payload).subscribe({
      next: () => this.lastSaved.set(new Date()),
      error: (err) => console.error('Sync failed', err)
    });
  }

  submit(): void {
    const sess = this.session();
    if (!sess || this.submitted) return;

    const payload = sess.exam.questions.map(q => ({
      question_id: q.id as number,
      selected_option_id: this.answers[q.id as number] ?? null
    }));

    this.api.submitSession(sess.session_id, payload).subscribe({
      next: (res) => {
        this.result.set(res);
        this.submitted = true;
      },
      error: (err) => this.error.set('Submission failed. Please try again.')
    });
  }

  loadReview(): void {
    const sessId = this.result()?.session_id;
    if (!sessId) return;

    this.api.getDetailedSession(sessId).subscribe({
      next: (data) => this.review.set(data),
      error: (err) => this.error.set('Failed to load detailed review.')
    });
  }
}
