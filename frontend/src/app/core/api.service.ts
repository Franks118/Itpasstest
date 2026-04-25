import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, catchError } from 'rxjs';
import { Exam, InProgressSession, Learner, ProgressSummary, SessionResult, StartSessionResponse, Topic, DetailedSession, RevealAnswerResponse } from './api.types';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private readonly baseUrl = 'http://localhost:8001/api';
  private readonly learnerIdKey = 'itpassport.learnerUserId';

  constructor(private readonly http: HttpClient) {}

  getTopics(): Observable<Topic[]> {
    return this.http.get<Topic[]>(`${this.baseUrl}/topics`);
  }

  getExams(): Observable<Exam[]> {
    return this.http.get<Exam[]>(`${this.baseUrl}/exams`);
  }

  getExam(id: number): Observable<Exam> {
    return this.http.get<Exam>(`${this.baseUrl}/exams/${id}`);
  }

  createExam(payload: unknown): Observable<Exam> {
    return this.http.post<Exam>(`${this.baseUrl}/exams`, payload);
  }

  createLearner(): Observable<Learner> {
    return this.http.post<Learner>(`${this.baseUrl}/learners`, {});
  }

  getLearner(id: number): Observable<Learner> {
    return this.http.get<Learner>(`${this.baseUrl}/learners/${id}`).pipe(
      catchError((err) => {
        if (err.status === 404) {
          localStorage.removeItem(this.learnerIdKey);
        }
        throw err;
      })
    );
  }

  startSession(userId: number, examId: number): Observable<StartSessionResponse> {
    return this.http.post<StartSessionResponse>(`${this.baseUrl}/sessions/start`, {
      user_id: userId,
      exam_id: examId,
    });
  }

  saveProgress(
    sessionId: number,
    userId: number,
    currentIndex: number,
    answers: Array<{ question_id: number; selected_option_id: number | null }>
  ): Observable<unknown> {
    return this.http.post(`${this.baseUrl}/sessions/${sessionId}/progress`, {
      user_id: userId,
      current_question_index: currentIndex,
      answers,
    });
  }

  submitSession(sessionId: number, answers: Array<{ question_id: number; selected_option_id: number | null }>): Observable<SessionResult> {
    return this.http.post<SessionResult>(`${this.baseUrl}/sessions/${sessionId}/submit`, {
      answers,
    });
  }

  getDetailedSession(sessionId: number): Observable<DetailedSession> {
    return this.http.get<DetailedSession>(`${this.baseUrl}/sessions/${sessionId}/detailed`);
  }

  getAnswerKey(examId: number): Observable<DetailedSession> {
    return this.http.get<DetailedSession>(`${this.baseUrl}/exams/${examId}/answer-key`);
  }

  revealAnswer(sessionId: number, questionId: number): Observable<RevealAnswerResponse> {
    return this.http.get<RevealAnswerResponse>(`${this.baseUrl}/sessions/${sessionId}/questions/${questionId}/reveal`);
  }

  getInProgressSessions(userId: number): Observable<InProgressSession[]> {
    return this.http.get<InProgressSession[]>(`${this.baseUrl}/users/${userId}/sessions/in-progress`);
  }

  getProgress(userId: number): Observable<ProgressSummary> {
    return this.http.get<ProgressSummary>(`${this.baseUrl}/users/${userId}/progress`);
  }

  getLearnerUserId(): number {
    const raw = localStorage.getItem(this.learnerIdKey);
    const value = Number(raw);
    return Number.isInteger(value) && value > 0 ? value : 0;
  }

  setLearnerUserId(userId: number): void {
    if (!Number.isInteger(userId) || userId <= 0) {
      return;
    }

    localStorage.setItem(this.learnerIdKey, String(userId));
  }
}
