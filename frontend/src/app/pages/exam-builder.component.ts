import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { ApiService } from '../core/api.service';
import { Topic } from '../core/api.types';

@Component({
  selector: 'app-exam-builder',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
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

        <h3>Questions</h3>
        <div formArrayName="questions">
          <div *ngFor="let question of questions.controls; let qIndex = index" [formGroupName]="qIndex" class="question-card">
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
              <h4>Options</h4>
              <div *ngFor="let _ of options(qIndex).controls; let oIndex = index" [formGroupName]="oIndex" class="option-row">
                <input class="input" type="text" formControlName="option_text" placeholder="Option text" />
                <label class="correct"><input type="radio" [name]="'correct-' + qIndex" [checked]="options(qIndex).at(oIndex).value.is_correct" (change)="setCorrect(qIndex, oIndex)" /> Correct</label>
              </div>
            </div>

            <div class="actions">
              <button class="btn btn-secondary" type="button" (click)="addOption(qIndex)">Add Option</button>
              <button class="btn btn-secondary" type="button" (click)="removeQuestion(qIndex)">Remove Question</button>
            </div>
          </div>
        </div>

        <p class="actions">
          <button class="btn btn-secondary" type="button" (click)="addQuestion()">Add Question</button>
          <button class="btn btn-primary" type="submit">Save Test</button>
          <a routerLink="/">Back</a>
        </p>
      </form>
    </section>
  `,
  styles: [`
    .page { display: grid; gap: 1rem; }
    .top { display: grid; gap: 0.5rem; }
    .hint { color: var(--muted-text); }
    .ok { color: var(--success-text); border-color: var(--success-border); background: var(--success-bg); }
    .error { color: var(--error-text); }
    .form { display: grid; gap: 1rem; }
    .base { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem; }
    .base label:nth-child(3) { grid-column: 1 / -1; }
    .question-card { border: 1px solid var(--border-color); border-radius: 12px; padding: 0.9rem; margin-bottom: 0.85rem; display: grid; gap: 0.7rem; background: var(--option-bg); }
    .option-row { display: grid; grid-template-columns: 1fr auto; gap: 0.55rem; align-items: center; margin-bottom: 0.4rem; }
    .correct { white-space: nowrap; display: flex; align-items: center; gap: 0.35rem; }
    .actions { display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center; }
    label { display: grid; gap: 0.35rem; }
  `]
})
export class ExamBuilderComponent implements OnInit {
  topics: Topic[] = [];
  message = '';
  error = '';
  form: FormGroup;

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
  }

  removeQuestion(index: number): void {
    this.questions.removeAt(index);
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
