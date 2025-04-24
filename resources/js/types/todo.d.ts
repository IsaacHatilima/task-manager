import { User } from '@/types/index';

export interface Todo {
    id: string;
    title: string;
    description: string;
    status: string;
    user: User;
}

export interface PaginatedTodos {
    current_page: number;
    data: Todo[];
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
