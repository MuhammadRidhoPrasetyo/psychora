// ============================================================
// Core Models
// ============================================================

export interface User {
    id: string;
    name: string;
    email: string;
    email_verified_at: string | null;
    avatar?: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    roles?: Role[];
    profile?: UserProfile;
}

export interface UserProfile {
    id: string;
    user_id: string;
    birth_date: string | null;
    gender: 'male' | 'female' | null;
    phone: string | null;
    province: string | null;
    city: string | null;
    education_level: string | null;
    target_program: string | null;
}

export interface Role {
    id: string;
    name: string;
    guard_name: string;
}

export interface Program {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    icon: string | null;
    is_active: boolean;
    sort_order: number;
    created_at: string;
    test_types?: TestType[];
    test_types_count?: number;
}

export interface TestType {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    engine_type: EngineType;
    is_active: boolean;
    sort_order: number;
}

export type EngineType = 'generic' | 'disc' | 'ist' | 'kraepelin' | 'psychotest';

// ============================================================
// Billing Models
// ============================================================

export interface SubscriptionPlan {
    id: string;
    name: string;
    code: string;
    description: string | null;
    price: number;
    duration_days: number;
    is_active: boolean;
    sort_order: number;
    entitlements?: PlanEntitlement[];
}

export interface PlanEntitlement {
    id: string;
    subscription_plan_id: string;
    program_id: string | null;
    test_type_id: string | null;
    program?: Program;
    test_type?: TestType;
}

export interface Subscription {
    id: string;
    user_id: string;
    subscription_plan_id: string;
    start_at: string;
    end_at: string;
    status: 'pending' | 'active' | 'expired' | 'cancelled';
    created_at: string;
    user?: User;
    plan?: SubscriptionPlan;
}

export interface Payment {
    id: string;
    user_id: string;
    subscription_id: string;
    invoice_number: string;
    amount: number;
    payment_method: string;
    status: 'pending' | 'paid' | 'failed' | 'refunded';
    paid_at: string | null;
    created_at: string;
    user?: User;
    subscription?: Subscription;
}

// ============================================================
// Test Package Models
// ============================================================

export interface TestPackage {
    id: string;
    program_id: string;
    name: string;
    slug: string;
    description: string | null;
    is_active: boolean;
    sort_order: number;
    program?: Program;
    items?: TestPackageItem[];
    items_count?: number;
}

export interface TestPackageItem {
    id: string;
    test_package_id: string;
    test_id: string;
    sort_order: number;
    test?: Test;
}

// ============================================================
// Generic Test Engine Models
// ============================================================

export interface Test {
    id: string;
    program_id: string;
    test_type_id: string;
    title: string;
    slug: string;
    description: string | null;
    duration_minutes: number | null;
    total_questions: number;
    pass_score: number | null;
    is_active: boolean;
    is_published: boolean;
    status: 'draft' | 'published' | 'archived';
    created_at: string;
    program?: Program;
    testType?: TestType;
    sections?: TestSection[];
    questions?: Question[];
    questions_count?: number;
}

export interface TestSection {
    id: string;
    test_id: string;
    title: string;
    description: string | null;
    sort_order: number;
    questions_count?: number;
}

export interface Question {
    id: string;
    test_id: string;
    test_section_id: string | null;
    question_type: 'multiple_choice' | 'essay' | 'true_false';
    question_text: string;
    explanation: string | null;
    score: number;
    sort_order: number;
    is_active: boolean;
    options?: QuestionOption[];
    essayAnswers?: QuestionEssayAnswer[];
    section?: TestSection;
}

export interface QuestionOption {
    id: string;
    question_id: string;
    option_text: string;
    is_correct: boolean;
    sort_order: number;
}

export interface QuestionEssayAnswer {
    id: string;
    question_id: string;
    answer_text: string;
    score: number;
    match_type: 'exact' | 'fuzzy' | 'contains' | 'regex';
    priority: number;
}

export interface TestAttempt {
    id: string;
    user_id: string;
    test_id: string;
    attempt_no: number;
    status: 'in_progress' | 'submitted' | 'scored' | 'expired';
    started_at: string;
    submitted_at: string | null;
    deadline_at: string | null;
    score: number | null;
    total_correct: number | null;
    total_wrong: number | null;
    total_unanswered: number | null;
    created_at: string;
    user?: User;
    test?: Test;
    answers?: AttemptAnswer[];
    result?: TestResult;
}

export interface AttemptAnswer {
    id: string;
    test_attempt_id: string;
    question_id: string;
    selected_option_id: string | null;
    answer_text: string | null;
    is_correct: boolean | null;
    score: number | null;
    answered_at: string | null;
    question?: Question;
}

export interface TestResult {
    id: string;
    test_attempt_id: string;
    user_id: string;
    test_id: string;
    total_score: number;
    percentage: number;
    is_passed: boolean;
    summary: Record<string, unknown> | null;
    created_at: string;
}

// ============================================================
// CPNS Models
// ============================================================

export interface CpnsTestBlueprint {
    id: string;
    test_id: string;
    category: 'TWK' | 'TIU' | 'TKP';
    total_questions: number;
    passing_score: number;
    test?: Test;
}

export interface CpnsScoreRule {
    id: string;
    test_id: string;
    category: 'TWK' | 'TIU' | 'TKP';
    correct_score: number;
    wrong_score: number;
    empty_score: number;
    test?: Test;
}

// ============================================================
// DISC Models
// ============================================================

export interface DiscForm {
    id: string;
    name: string;
    description: string | null;
    is_active: boolean;
    created_at: string;
    questions?: DiscQuestion[];
    questions_count?: number;
}

export interface DiscQuestion {
    id: string;
    disc_form_id: string;
    question_number: number;
    is_active: boolean;
    options?: DiscOption[];
}

export interface DiscOption {
    id: string;
    disc_question_id: string;
    option_text: string;
    sort_order: number;
    scorings?: DiscOptionScoring[];
}

export interface DiscOptionScoring {
    id: string;
    disc_option_id: string;
    response_type: 'most' | 'least';
    disc_code: 'D' | 'I' | 'S' | 'C' | 'star';
    score_value: number;
}

export interface DiscAttempt {
    id: string;
    user_id: string;
    disc_form_id: string;
    attempt_number: number;
    status: 'in_progress' | 'submitted' | 'scored';
    score: number | null;
    deadline_at: string | null;
    started_at: string;
    submitted_at: string | null;
    form?: DiscForm;
    answers?: DiscAnswer[];
    result?: DiscResult;
}

export interface DiscAnswer {
    id: string;
    disc_attempt_id: string;
    disc_question_id: string;
    most_option_id: string | null;
    least_option_id: string | null;
    answered_at: string | null;
}

export interface DiscResult {
    id: string;
    disc_attempt_id: string;
    most_d: number;
    most_i: number;
    most_s: number;
    most_c: number;
    most_star: number;
    least_d: number;
    least_i: number;
    least_s: number;
    least_c: number;
    least_star: number;
    score_d: number;
    score_i: number;
    score_s: number;
    score_c: number;
    dominant_profile: string | null;
    interpretation: string | null;
}

// ============================================================
// IST Models
// ============================================================

export interface IstForm {
    id: string;
    name: string;
    description: string | null;
    is_active: boolean;
    created_at: string;
    subtests?: IstSubtest[];
    formItems?: IstFormItem[];
}

export interface IstSubtest {
    id: string;
    subtest_code: string;
    subtest_name: string;
    sort_order: number;
    duration_minutes: number;
    max_score: number;
    questions_count?: number;
}

export interface IstFormItem {
    id: string;
    ist_form_id: string;
    ist_subtest_id: string;
    sort_order: number;
    is_randomized: boolean;
    number_of_questions: number | null;
    multiplier: number;
    clue_first: boolean;
    duration_minutes: number | null;
    minimum_score: number | null;
    subtest?: IstSubtest;
}

export interface IstInstruction {
    id: string;
    ist_form_id: string;
    ist_subtest_id: string | null;
    title: string;
    content: string;
    sort_order: number;
}

export interface IstClue {
    id: string;
    ist_form_id: string;
    ist_subtest_id: string | null;
    clue_text: string;
    duration: number | null;
}

export interface IstAttempt {
    id: string;
    user_id: string;
    ist_form_id: string;
    attempt_number: number;
    current_subtest_code: string | null;
    status: 'in_progress' | 'submitted' | 'scored';
    total_score: number | null;
    iq_score: number | null;
    started_at: string;
    submitted_at: string | null;
    form?: IstForm;
    subtestAttempts?: IstSubtestAttempt[];
    results?: IstResult[];
}

export interface IstSubtestAttempt {
    id: string;
    ist_attempt_id: string;
    ist_subtest_id: string;
    subtest_code: string;
    status: 'pending' | 'in_progress' | 'submitted';
    raw_score: number | null;
    scaled_score: number | null;
    random_seed: number | null;
    started_at: string | null;
    submitted_at: string | null;
    subtest?: IstSubtest;
}

export interface IstAnswer {
    id: string;
    ist_attempt_id: string;
    ist_subtest_id: string;
    question_id: string;
    selected_option_id: string | null;
    answer_text: string | null;
    answer_json: Record<string, unknown> | null;
    is_correct: boolean | null;
    score: number | null;
}

export interface IstResult {
    id: string;
    ist_attempt_id: string;
    category: 'verbal' | 'numerical' | 'figural' | 'overall';
    raw_score: number;
    scaled_score: number | null;
    percentile: number | null;
    interpretation: string | null;
}

// ============================================================
// Kraepelin Models
// ============================================================

export interface KraepelinForm {
    id: string;
    title: string;
    description: string | null;
    is_active: boolean;
    created_at: string;
}

export interface KraepelinAttempt {
    id: string;
    user_id: string;
    kraepelin_form_id: string;
    attempt_number: number;
    numbers_per_column: number;
    columns_count: number;
    duration_per_column: number;
    status: 'in_progress' | 'submitted' | 'scored';
    speed_score: number | null;
    accuracy_score: number | null;
    stability_score: number | null;
    total_skipped: number | null;
    started_at: string;
    submitted_at: string | null;
    form?: KraepelinForm;
    columns?: KraepelinAttemptColumn[];
}

export interface KraepelinAttemptColumn {
    id: string;
    kraepelin_attempt_id: string;
    column_number: number;
    started_at: string | null;
    submitted_at: string | null;
    numbers?: KraepelinAttemptNumber[];
}

export interface KraepelinAttemptNumber {
    id: string;
    kraepelin_attempt_column_id: string;
    position: number;
    number_value: number;
}

export interface KraepelinAnswer {
    id: string;
    kraepelin_attempt_id: string;
    kraepelin_attempt_column_id: string;
    position: number;
    top_number: number;
    bottom_number: number;
    user_answer: number | null;
    correct_answer: number;
    is_correct: boolean | null;
}

// ============================================================
// Psychotest Models
// ============================================================

export interface PsychotestAspect {
    id: string;
    code: string;
    name: string;
    description: string | null;
    sort_order: number;
    characteristics?: PsychotestCharacteristic[];
    characteristics_count?: number;
}

export interface PsychotestCharacteristic {
    id: string;
    psychotest_aspect_id: string;
    code: string;
    name: string;
    description: string | null;
    sort_order: number;
    scores?: PsychotestCharacteristicScore[];
    aspect?: PsychotestAspect;
}

export interface PsychotestCharacteristicScore {
    id: string;
    psychotest_characteristic_id: string;
    min_score: number;
    max_score: number;
    level: string;
    interpretation: string;
}

export interface PsychotestForm {
    id: string;
    name: string;
    description: string | null;
    is_active: boolean;
    created_at: string;
    questions?: PsychotestQuestion[];
    questions_count?: number;
}

export interface PsychotestQuestion {
    id: string;
    psychotest_form_id: string;
    question_number: number;
    is_active: boolean;
    options?: PsychotestQuestionOption[];
}

export interface PsychotestQuestionOption {
    id: string;
    psychotest_question_id: string;
    label: string;
    statement: string;
    sort_order: number;
    mappings?: PsychotestOptionCharacteristicMapping[];
}

export interface PsychotestOptionCharacteristicMapping {
    id: string;
    psychotest_question_option_id: string;
    psychotest_aspect_id: string;
    psychotest_characteristic_id: string;
    weight: number;
    aspect?: PsychotestAspect;
    characteristic?: PsychotestCharacteristic;
}

export interface PsychotestAttempt {
    id: string;
    user_id: string;
    psychotest_form_id: string;
    attempt_number: number;
    status: 'in_progress' | 'submitted' | 'scored';
    score: number | null;
    deadline_at: string | null;
    started_at: string;
    submitted_at: string | null;
    form?: PsychotestForm;
    answers?: PsychotestAnswer[];
    resultCharacteristics?: PsychotestResultCharacteristic[];
    resultAspects?: PsychotestResultAspect[];
}

export interface PsychotestAnswer {
    id: string;
    psychotest_attempt_id: string;
    psychotest_question_id: string;
    psychotest_question_option_id: string | null;
    answered_at: string | null;
}

export interface PsychotestResultCharacteristic {
    id: string;
    psychotest_attempt_id: string;
    psychotest_characteristic_id: string;
    raw_score: number;
    scaled_score: number | null;
    characteristic?: PsychotestCharacteristic;
}

export interface PsychotestResultAspect {
    id: string;
    psychotest_attempt_id: string;
    psychotest_aspect_id: string;
    raw_score: number;
    scaled_score: number | null;
    aspect?: PsychotestAspect;
}

// ============================================================
// Supportive Models
// ============================================================

export interface Bookmark {
    id: string;
    user_id: string;
    question_id: string;
    notes: string | null;
    created_at: string;
    question?: Question;
}

export interface UserProgress {
    id: string;
    user_id: string;
    program_id: string | null;
    test_type_id: string | null;
    total_attempts: number;
    total_correct: number;
    total_questions_attempted: number;
    average_score: number;
    program?: Program;
    testType?: TestType;
}

export interface ActivityLog {
    id: string;
    user_id: string | null;
    action: string;
    description: string | null;
    subject_type: string | null;
    subject_id: string | null;
    properties: Record<string, unknown> | null;
    ip_address: string | null;
    user_agent: string | null;
    created_at: string;
    user?: User;
}

// ============================================================
// Utility Types
// ============================================================

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface DashboardStats {
    total_users: number;
    total_subscriptions: number;
    total_revenue: number;
    total_attempts: number;
    recent_users?: User[];
    recent_attempts?: TestAttempt[];
}
