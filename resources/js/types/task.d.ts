import { User } from '@/types/index';

export interface Task {
    id: string;
    title: string;
    description: string;
    status: string;
    created_at: string;
    updated_at: string;
    user: User;
}

export interface PaginatedTask {
    current_page: number;
    data: Task[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | URL;
    path: string;
    per_page: number;
    prev_page_url: string | URL;
    to: number;
    total: number;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface TaskFilters {
    title: string | null;
    status: string | null;
    assigned_to: string | null;
}

export interface TaskStats {
    pending: number;
    in_progress: number;
    cancelled: number;
    completed: number;
}
