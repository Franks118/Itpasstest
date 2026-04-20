export interface Topic {
  id: number;
  name: string;
  major_category: string;
  middle_category: string;
}

export interface QuestionOption {
  id?: number;
  option_text: string;
  is_correct?: boolean;
  order_index?: number;
}

export interface ExamQuestion {
  id?: number;
  topic_id: number;
  topic_name?: string;
  question_text: string;
  difficulty?: 'easy' | 'medium' | 'hard';
  explanation?: string | null;
  points?: number;
  order_index?: number;
  options: QuestionOption[];
}

export interface Exam {
  id: number;
  user_id: number;
  title: string;
  description: string | null;
  duration_minutes: number;
  total_questions: number;
  status: string;
  questions?: ExamQuestion[];
  questions_count?: number;
}

export interface StartSessionResponse {
  session_id: number;
  exam: {
    id: number;
    title: string;
    description: string | null;
    duration_minutes: number;
    questions: ExamQuestion[];
  };
}

export interface SessionResult {
  session_id: number;
  score: number;
  correct_answers: number;
  total_questions: number;
}

export interface ProgressSummary {
  user: {
    id: number;
    name: string;
    session_number: string;
  };
  stats: {
    total_sessions: number;
    average_score: number;
    best_score: number;
    total_questions_answered: number;
  };
  recent_sessions: Array<{
    id: number;
    exam_title: string;
    score: number;
    correct_answers: number;
    total_questions: number;
    submitted_at: string;
  }>;
  topic_progress: Array<{
    topic_id: number;
    topic_name: string;
    major_category: string;
    middle_category: string;
    attempts_count: number;
    correct_answers: number;
    total_answers: number;
    mastery_percent: number;
    last_attempted_at: string;
  }>;
}
