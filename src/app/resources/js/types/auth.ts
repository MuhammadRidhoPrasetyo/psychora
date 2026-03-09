export type Role = {
    id: number;
    name: string;
    guard_name: string;
};

export type User = {
    id: string;
    name: string;
    email: string;
    phone?: string | null;
    avatar?: string;
    is_active: boolean;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles?: Role[];
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};

export type Flash = {
    success: string | null;
    error: string | null;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
