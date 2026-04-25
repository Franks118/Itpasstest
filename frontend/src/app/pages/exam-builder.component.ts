import { CommonModule } from '@angular/common';
import { Component, OnInit, signal, computed } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators, AbstractControl } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { ApiService } from '../core/api.service';
import { Topic } from '../core/api.types';

@Component({
  selector: 'app-exam-builder',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink, MatPaginatorModule],
  template: `
    <section class="page">
      <div class="card top">
        <h2>Create IT Passport Test</h2>
        <p class="hint">Use integrated topic list to quickly build custom exams.</p>
      </div>
      <p *ngIf="message" class="ok card">{{ message }}</p>
      <p *ngIf="error" class="error">{{ error }}</p>

      <form [formGroup]="form" (ngSubmit)="save()" class="card form">
        <div class="base">
          <label>Creator User ID <input class="input" type="number" formControlName="user_id" min="1" /></label>
          <label>Title <input class="input" type="text" formControlName="title" /></label>
          <label>Description <textarea formControlName="description"></textarea></label>
          <label>Duration (minutes) <input class="input" type="number" formControlName="duration_minutes" min="1" /></label>
        </div>

        <div class="questions-header">
          <h3>Questions ({{ questions.length }})</h3>
          <mat-paginator *ngIf="questions.length > pageSize()"
            [length]="questions.length"
            [pageSize]="pageSize()"
            [pageSizeOptions]="[5, 10, 20]"
            (page)="onPageChange($event)"
            aria-label="Select page of questions">
          </mat-paginator>
        </div>

        <div formArrayName="questions">
          <div *ngFor="let question of paginatedQuestions(); let i = index" [formGroupName]="getGlobalIndex(i)" class="question-card">
            <div class="question-header-row">
              <h4>Question #{{ getGlobalIndex(i) + 1 }}</h4>
              <button class="btn btn-secondary btn-sm" type="button" (click)="removeQuestion(getGlobalIndex(i))">Remove</button>
            </div>

            <label>
              Topic
              <select class="input" formControlName="topic_id">
                <option [ngValue]="null">Select topic</option>
                <option *ngFor="let topic of topics" [ngValue]="topic.id">
                  {{ topic.major_category }} / {{ topic.name }}
                </option>
              </select>
            </label>
            <label>Question Text <textarea formControlName="question_text"></textarea></label>
            <label>
              Difficulty
              <select class="input" formControlName="difficulty">
                <option value="easy">easy</option>
                <option value="medium">medium</option>
                <option value="hard">hard</option>
              </select>
            </label>

            <div formArrayName="options">
              <h5>Options</h5>
              <div *ngFor="let _ of options(getGlobalIndex(i)).controls; let oIndex = index" [formGroupName]="oIndex" class="option-row">
                <input class="input" type="text" formControlName="option_text" placeholder="Option text" />
                <label class="correct">
                  <input type="radio" 
                         [name]="'correct-' + getGlobalIndex(i)" 
                         [checked]="options(getGlobalIndex(i)).at(oIndex).value.is_correct" 
                         (change)="setCorrect(getGlobalIndex(i), oIndex)" /> 
                  Correct
                </label>
              </div>
              <button class="btn btn-secondary btn-sm" type="button" (click)="addOption(getGlobalIndex(i))">Add Option</button>
            </div>
          </div>
        </div>

        <div class="pagination-footer-builder" *ngIf="questions.length > pageSize()">
          <mat-paginator
            [length]="questions.length"
            [pageSize]="pageSize()"
            [pageSizeOptions]="[5, 10, 20]"
            [pageIndex]="pageIndex()"
            (page)="onPageChange($event)"
            aria-label="Select page of questions">
          </mat-paginator>
        </div>

        <div class="form-actions">
          <button class="btn btn-secondary" type="button" (click)="addQuestion()">Add Question</button>
          <div class="spacer"></div>
          <a routerLink="/" class="btn btn-link">Back</a>
          <button class="btn btn-primary" type="submit">Save Test</button>
        </div>
      </form>
    </section>
  `,
  styles: [`
    .page { display: grid; gap: 1rem; }
    .top { display: grid; gap: 0.5rem; }
    .hint { color: var(--text-muted); }
    .ok { color: var(--success-text); border-color: var(--success-border); background: var(--success-bg); }
    .error { color: var(--error-text); }
    .form { display: grid; gap: 1rem; }
    .base { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem; }
    .base label:nth-child(3) { grid-column: 1 / -1; }
    
    .questions-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-top: 1rem; }
    .question-card { border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; display: grid; gap: 0.75rem; background: var(--surface-bg); }
    .question-header-row { display: flex; justify-content: space-between; align-items: center; }
    
    .option-row { display: grid; grid-template-columns: 1fr auto; gap: 0.75rem; align-items: center; margin-bottom: 0.5rem; }
    .correct { white-space: nowrap; display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; }
    
    .form-actions { display: flex; gap: 1rem; align-items: center; margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; }
    .spacer { flex: 1; }
    .btn-sm { padding: 0.25rem 0.75rem; font-size: 0.8rem; }
    .btn-link { text-decoration: none; color: var(--text-muted); }
    
    label { display: grid; gap: 0.35rem; font-weight: 600; font-size: 0.9rem; }
    h4, h5 { margin: 0; }
    
    mat-paginator { 
      background: var(--surface-bg); 
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-md);
      border: none;
    }
  `]
})
export class ExamBuilderComponent implements OnInit {
  topics: Topic[] = [];
  message = '';
  error = '';
  form: FormGroup;

  pageIndex = signal(0);
  pageSize = signal(5);

  constructor(
    private readonly fb: FormBuilder,
    private readonly api: ApiService
  ) {
    this.form = this.fb.group({
      user_id: [1, [Validators.required, Validators.min(1)]],
      title: ['', [Validators.required]],
      description: [''],
      duration_minutes: [60, [Validators.required, Validators.min(1)]],
      questions: this.fb.array([]),
    });
  }

  ngOnInit(): void {
    this.addQuestion();
    this.api.getTopics().subscribe({
      next: (topics) => (this.topics = topics),
      error: () => (this.error = 'Failed to load topics.'),
    });
  }

  get questions(): FormArray {
    return this.form.get('questions') as FormArray;
  }

  paginatedQuestions(): AbstractControl[] {
    const start = this.pageIndex() * this.pageSize();
    return this.questions.controls.slice(start, start + this.pageSize());
  }

  getGlobalIndex(localIndex: number): number {
    return (this.pageIndex() * this.pageSize()) + localIndex;
  }

  onPageChange(event: PageEvent): void {
    this.pageIndex.set(event.pageIndex);
    this.pageSize.set(event.pageSize);
  }

  options(questionIndex: number): FormArray {
    return this.questions.at(questionIndex).get('options') as FormArray;
  }

  addQuestion(): void {
    const question = this.fb.group({
      topic_id: [null, Validators.required],
      question_text: ['', Validators.required],
      difficulty: ['medium'],
      options: this.fb.array([
        this.fb.group({ option_text: ['', Validators.required], is_correct: [true] }),
        this.fb.group({ option_text: ['', Validators.required], is_correct: [false] }),
      ]),
    });
    this.questions.push(question);
    
    // Switch to the last page to show the new question
    const lastPage = Math.floor((this.questions.length - 1) / this.pageSize());
    this.pageIndex.set(lastPage);
  }

  removeQuestion(index: number): void {
    this.questions.removeAt(index);
    // Adjust page index if the current page becomes empty
    const maxPage = Math.max(0, Math.ceil(this.questions.length / this.pageSize()) - 1);
    if (this.pageIndex() > maxPage) {
      this.pageIndex.set(maxPage);
    }
  }

  addOption(questionIndex: number): void {
    this.options(questionIndex).push(this.fb.group({
      option_text: ['', Validators.required],
      is_correct: [false],
    }));
  }

  setCorrect(questionIndex: number, optionIndex: number): void {
    this.options(questionIndex).controls.forEach((control, idx) => {
      control.patchValue({ is_correct: idx === optionIndex });
    });
  }

  save(): void {
    this.message = '';
    this.error = '';
    if (this.form.invalid) {
      this.error = 'Please fill all required fields.';
      return;
    }

    this.api.createExam(this.form.getRawValue()).subscribe({
      next: (exam) => {
        this.message = `Test "${exam.title}" created successfully.`;
        this.form.patchValue({ title: '', description: '' });
        while (this.questions.length > 0) {
          this.questions.removeAt(0);
        }
        this.addQuestion();
      },
      error: (err) => {
        this.error = err?.error?.message ?? 'Unable to create test.';
      },
    });
  }
}
