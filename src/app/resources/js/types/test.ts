export type TestType = {
    id: string;
    code: string;
    name: string;
    engine_type: 'generic' | 'disc' | 'ist' | 'kraepelin' | 'papikostick';
    description: string | null;
};

export type Program = {
    id: string;
    code: string;
    name: string;
    description: string | null;
};

export type TestSection = {
    id: string;
    test_id: string;
    title: string;
    instruction: string | null;
    duration_minutes: number | null;
    sort_order: number;
};

export type QuestionOption = {
    id: string;
    question_id: string;
    option_key: string;
    content: string;
    sort_order: number;
    // Note: is_correct and score are NOT sent to client during test-taking
};

export type Question = {
    id: string;
    test_id: string;
    test_section_id: string | null;
    question_type: 'multiple_choice' | 'multi_select' | 'essay' | 'true_false' | 'number_input' | 'matrix';
    content: string;
    media_url: string | null;
    difficulty: 'easy' | 'medium' | 'hard' | null;
    sort_order: number;
    options: QuestionOption[];
    section?: TestSection;
};

export type TestSummary = {
    id: string;
    title: string;
    slug: string;
    description: string | null;
    duration_minutes: number | null;
    total_questions: number | null;
    visibility: 'free' | 'premium' | 'private';
    status: 'draft' | 'published' | 'archived';
    test_type: TestType;
    program: Program;
    user_attempts_count?: number;
};

export type TestDetail = TestSummary & {
    instruction: string | null;
    sections: TestSection[];
    scoring_method: 'standard' | 'weighted' | 'profile' | 'manual';
};

export type AttemptAnswer = {
    question_id: string;
    selected_option_id: string | null;
    answer_text: string | null;
};

export type TestAttempt = {
    id: string;
    user_id: string;
    test_id: string;
    attempt_no: number;
    started_at: string | null;
    submitted_at: string | null;
    expired_at: string | null;
    status: 'draft' | 'in_progress' | 'submitted' | 'expired' | 'evaluated';
    total_score: number | null;
    percentage: number | null;
    test: TestSummary;
};

export type ActiveAttempt = {
    id: string;
    test_id: string;
    attempt_no: number;
    started_at: string;
    expired_at: string | null;
    status: string;
    test: TestDetail;
    questions: Question[];
    answers: AttemptAnswer[];
};

export type AttemptResultAnswer = {
    question_id: string;
    question_content: string;
    question_type: string;
    selected_option_id: string | null;
    answer_text: string | null;
    is_correct: boolean | null;
    score: number | null;
    correct_option_id: string | null;
    explanation: string | null;
    options: QuestionOption[];
};

export type AttemptResult = {
    attempt: TestAttempt;
    result: {
        id: string;
        raw_score: number | null;
        final_score: number | null;
        percentage: number | null;
        interpretation: string | null;
    } | null;
    answers: AttemptResultAnswer[];
    stats: {
        total: number;
        correct: number;
        wrong: number;
        unanswered: number;
    };
};
