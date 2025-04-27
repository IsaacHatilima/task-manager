import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import Create from '@/pages/todo/partials/create';
import { type BreadcrumbItem } from '@/types';
import { PaginatedTodos, Todo, TodoFilters } from '@/types/todo';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { debounce } from 'lodash';
import { useEffect, useMemo } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Todos',
        href: route('todos.index'),
    },
];

export default function Index() {
    const todos: PaginatedTodos = usePage().props.todos as PaginatedTodos;
    const todoStatus: Array<string> = usePage().props.todoStatus as Array<string>;
    const statusColorMap: Record<string, string> = {
        completed: 'border-green-500 text-green-600',
        canceled: 'border-red-500 text-red-600',
        pending: 'border-amber-500 text-amber-600',
        in_progress: 'border-blue-500 text-blue-600',
    };
    const deletedTodoMessage: string = usePage().props.deletedTodoMessage as string;
    const filters: TodoFilters = usePage().props.filters as TodoFilters;
    const { data, setData } = useForm({
        title: filters?.title || '',
        description: filters?.description || '',
        status: filters?.status || '',
    });

    const debouncedSearch = useMemo(() => {
        return debounce(() => {
            const filtersApplied = Object.keys(data).some((key) => data[key as keyof TodoFilters] !== '' && data[key as keyof TodoFilters] !== null);

            const params: Record<string, string | number> = {
                ...data,
                page: filtersApplied ? 1 : todos.current_page,
            };

            Object.keys(params).forEach((key) => {
                if (params[key] === '' || params[key] === null || params[key] === 'all') {
                    delete params[key];
                }
            });

            router.get(route('todos.index'), params, {
                preserveState: true,
                preserveScroll: true,
            });
        }, 300);
    }, [data, todos.current_page]);

    useEffect(() => {
        debouncedSearch();
    }, [data, debouncedSearch]);

    useEffect(() => {
        if (deletedTodoMessage && deletedTodoMessage !== '401') {
            toast.success('Success', { description: deletedTodoMessage });
        }
    }, [deletedTodoMessage]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Todo" />
            <div className="flex justify-center">
                <div className="w-full">
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle>Todos</CardTitle>
                                <Create isMain={true} />
                            </div>
                            <CardDescription>List of your Todos</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableCaption>A list of your Todos.</TableCaption>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Title</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Description</TableHead>
                                        <TableHead className="ml-4 text-right">Action</TableHead>
                                    </TableRow>
                                    <TableRow>
                                        <TableHead>
                                            <Input
                                                className="font-medium"
                                                id="title"
                                                name="title"
                                                placeholder="Title"
                                                value={data.title}
                                                onChange={(e) => {
                                                    setData('title', e.target.value);
                                                }}
                                            />
                                        </TableHead>
                                        <TableHead>
                                            <Select value={data.status} onValueChange={(value) => setData('status', value)}>
                                                <SelectTrigger className="mt-1 w-full">
                                                    <SelectValue placeholder="Select Status" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <SelectLabel>Status</SelectLabel>
                                                        <SelectItem value="all">All</SelectItem>
                                                        {todoStatus.map((g) => (
                                                            <SelectItem key={g} value={g}>
                                                                {g
                                                                    .split('_')
                                                                    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                                                                    .join(' ')}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                        </TableHead>
                                        <TableHead>
                                            <Input
                                                className="font-medium"
                                                id="description"
                                                name="description"
                                                placeholder="Description"
                                                value={data.description}
                                                onChange={(e) => {
                                                    setData('description', e.target.value);
                                                }}
                                            />
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {todos.data.map((todo: Todo) => (
                                        <TableRow key={todo.id}>
                                            <TableCell className="font-medium">{todo.title}</TableCell>
                                            <TableCell>
                                                <Badge variant="outline" className={statusColorMap[todo.status]}>
                                                    {todo.status
                                                        .split('_')
                                                        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                                                        .join(' ')}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                {' '}
                                                {todo.description.length > 80 ? `${todo.description.slice(0, 80)}...` : todo.description}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex gap-3">
                                                    <Create isMain={false} todo={todo} />
                                                    <Link href={route('todos.show', todo.id)} className="text-blue-500 hover:underline">
                                                        View
                                                    </Link>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                        <CardFooter>
                            <Pagination>
                                <PaginationContent>
                                    <PaginationItem className="cursor-pointer">
                                        <PaginationPrevious
                                            onClick={() => {
                                                if (todos.prev_page_url) {
                                                    router.visit(todos.prev_page_url);
                                                }
                                            }}
                                        />
                                    </PaginationItem>

                                    {todos.links.map((page, index) => {
                                        if (index === 0 || index === todos.links.length - 1) {
                                            return null; // Skip default "Previous" and "Next"
                                        }

                                        const currentPage = todos.current_page;
                                        const totalPages = todos.last_page;

                                        const pageNumber = Number(page.label);
                                        if (isNaN(pageNumber)) return null; // skip if label isn't a number

                                        // Shows first 5, last 5, and 5 around current page
                                        if (
                                            pageNumber <= 5 || // first 5 pages
                                            pageNumber > totalPages - 5 || // last 5 pages
                                            (pageNumber >= currentPage - 2 && pageNumber <= currentPage + 2) // current +- 2
                                        ) {
                                            return (
                                                <PaginationItem key={index} className="cursor-pointer">
                                                    <PaginationLink
                                                        isActive={page.active}
                                                        onClick={() => {
                                                            if (page?.url) {
                                                                router.visit(page?.url);
                                                            }
                                                        }}
                                                    >
                                                        {page.label}
                                                    </PaginationLink>
                                                </PaginationItem>
                                            );
                                        }

                                        return null; // Else don't render anything here yet
                                    })}

                                    <PaginationItem className="cursor-pointer">
                                        <PaginationNext
                                            onClick={() => {
                                                if (todos.next_page_url) {
                                                    router.visit(todos.next_page_url);
                                                }
                                            }}
                                        />
                                    </PaginationItem>
                                </PaginationContent>
                            </Pagination>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
