import { Injectable, signal } from '@angular/core';
import { ApiService } from './api.service';
import { Learner } from './api.types';
import { catchError, map, of, tap } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class LearnerService {
  learner = signal<Learner | null>(null);

  constructor(private readonly api: ApiService) {
    const savedId = this.api.getLearnerUserId();
    if (savedId > 0) {
      this.validateAndSetLearner(savedId).subscribe();
    } else {
      this.generateNewLearner();
    }
  }

  generateNewLearner(): void {
    this.api.createLearner().subscribe({
      next: (learner) => this.setLearner(learner),
      error: (err) => console.error('Failed to auto-generate Learner ID', err)
    });
  }

  validateAndSetLearner(id: number) {
    return this.api.getLearner(id).pipe(
      tap((learner) => this.setLearner(learner)),
      catchError((err) => {
        console.error('Invalid Learner ID', err);
        this.generateNewLearner();
        return of(null);
      })
    );
  }

  switchLearner(id: number) {
    return this.api.getLearner(id).pipe(
      tap((learner) => this.setLearner(learner))
    );
  }

  private setLearner(learner: Learner): void {
    this.learner.set(learner);
    this.api.setLearnerUserId(learner.id);
  }
}
