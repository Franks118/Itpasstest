import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Exam, ProgressSummary, SessionResult, StartSessionResponse, Topic } from './api.types';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private readonly baseUrl = 'http://127.0.0.1:8000/api';
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

  startSession(userId: number, examId: number): Observable<StartSessionResponse> {
    return this.http.post<StartSessionResponse>(`${this.baseUrl}/sessions/start`, {
      user_id: userId,
      exam_id: examId,
    });
  }

  submitSession(sessionId: number, answers: Array<{ question_id: number; selected_option_id: number | null }>): Observable<SessionResult> {
    return this.http.post<SessionResult>(`${this.baseUrl}/sessions/${sessionId}/submit`, {
      answers,
    });
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
