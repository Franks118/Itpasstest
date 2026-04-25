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
  resumed: boolean;
  progress: {
    current_question_index: number;
    answers: Array<{
      question_id: number;
      selected_option_id: number | null;
    }>;
    saved_at: string | null;
  };
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
  pass_score: number;
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
  exam_attempts: Array<{
    exam_id: number;
    exam_title: string;
    attempts: number;
    passed_attempts: number;
    pass_rate: number;
  }>;
}

export interface Learner {
  id: number;
  name: string;
  session_number: number;
  score: number;
  timestamp: string;
}

export interface InProgressSession {
  id: number;
  user_id: number;
  exam_id: number;
  current_question_index: number;
  total_questions: number;
  updated_at: string;
  exam: {
    id: number;
    title: string;
    description: string;
    total_questions: number;
  };
}

export interface DetailedSession {
  session?: {
    id: number;
    score: number;
    correct_answers: number;
    total_questions: number;
    submitted_at: string;
  };
  exam: {
    id: number;
    title: string;
    questions: Array<{
      id: number;
      topic_name: string;
      question_text: string;
      explanation: string | null;
      difficulty: string;
      selected_option_id: number | null;
      is_correct: boolean;
      options: Array<{
        id: number;
        option_text: string;
        is_correct: boolean;
      }>;
    }>;
  };
}

export interface RevealAnswerResponse {
  question_id: number;
  explanation: string | null;
  correct_option_id: number;
}
