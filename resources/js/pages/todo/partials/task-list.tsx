import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { PaginatedTask, Task, TaskFilters, TaskStats } from '@/types/task';
import { Todo } from '@/types/todo';
import { Link, router, useForm, usePage } from '@inertiajs/react';
import { debounce } from 'lodash';
import { useEffect, useMemo } from 'react';

export default function TaskList({ todo, taskCounts, todoStatus }: { todo: Todo; taskCounts: TaskStats; todoStatus: Array<string> }) {
    const tasks: PaginatedTask = usePage().props.todoTasks as PaginatedTask;
    const statusColorMap: Record<string, string> = {
        completed: 'border-green-500 text-green-600',
        cancelled: 'border-red-500 text-red-600',
        pending: 'border-amber-500 text-amber-600',
        in_progress: 'border-blue-500 text-blue-600',
    };

    const filters: TaskFilters = usePage().props.filters as TaskFilters;
    const { data, setData } = useForm({
        title: filters?.title || '',
        status: filters?.status || '',
        assigned_to: filters?.assigned_to || '',
    });

    const debouncedSearch = useMemo(() => {
        return debounce(() => {
            const filtersApplied = Object.keys(data).some((key) => data[key as keyof TaskFilters] !== '' && data[key as keyof TaskFilters] !== null);

            const params: Record<string, string | number> = {
                ...data,
                page: filtersApplied ? 1 : tasks.current_page,
            };

            Object.keys(params).forEach((key) => {
                if (params[key] === '' || params[key] === null || params[key] === 'all') {
                    delete params[key];
                }
            });

            router.get(route('todos.show', todo.id), params, {
                preserveState: true,
                preserveScroll: true,
            });
        }, 300);
    }, [data, tasks.current_page, todo]);

    useEffect(() => {
        debouncedSearch();
        return () => {
            debouncedSearch.cancel();
        };
    }, [data, debouncedSearch]);

    return (
        <Card>
            <CardHeader>
                <CardTitle>{todo.title} Tasks</CardTitle>
                <CardDescription className="flex gap-3">
                    <TodoStats statusColorMap={statusColorMap} status="pending" count={taskCounts.pending.toString()} tooltip="Pending" />
                    <TodoStats statusColorMap={statusColorMap} status="in_progress" count={taskCounts.in_progress.toString()} tooltip="In Progress" />
                    <TodoStats statusColorMap={statusColorMap} status="completed" count={taskCounts.completed.toString()} tooltip="Completed" />
                    <TodoStats statusColorMap={statusColorMap} status="cancelled" count={taskCounts.cancelled.toString()} tooltip="Cancelled" />
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableCaption>A list of your Tasks.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Title</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Assigned</TableHead>
                            <TableHead className="ml-4 text-right">Action</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead>
                                <Input
                                    className="font-medium"
                                    id="title"
                                    name="title"
                                    type="text"
                                    placeholder="Search Title"
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
                                    id="assigned_to"
                                    name="assigned_to"
                                    type="text"
                                    placeholder="Search Full Name"
                                    value={data.assigned_to}
                                    onChange={(e) => {
                                        setData('assigned_to', e.target.value);
                                    }}
                                />
                            </TableHead>
                            <TableHead className="ml-4 text-right"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {tasks.data.map((task: Task) => (
                            <TableRow key={task.id}>
                                <TableCell className="font-medium">{task.title}</TableCell>
                                <TableCell>
                                    <Badge variant="outline" className={statusColorMap[task.status]}>
                                        {task.status
                                            .split('_')
                                            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                                            .join(' ')}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    {' '}
                                    {task.user.profile.first_name} {task.user.profile.last_name}{' '}
                                </TableCell>
                                <TableCell className="text-right">
                                    <div className="flex gap-3">
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
                                    if (tasks.prev_page_url) {
                                        router.visit(tasks.prev_page_url);
                                    }
                                }}
                            />
                        </PaginationItem>

                        {tasks.links.map((page, index) => {
                            if (index === 0 || index === tasks.links.length - 1) {
                                return null; // Skip default "Previous" and "Next"
                            }

                            const currentPage = tasks.current_page;
                            const totalPages = tasks.last_page;

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
                                    if (tasks.next_page_url) {
                                        router.visit(tasks.next_page_url);
                                    }
                                }}
                            />
                        </PaginationItem>
                    </PaginationContent>
                </Pagination>
            </CardFooter>
        </Card>
    );
}

function TodoStats({
    statusColorMap,
    status,
    count,
    tooltip,
}: {
    statusColorMap: Record<string, string>;
    status: string;
    count: string;
    tooltip: string;
}) {
    return (
        <TooltipProvider>
            <Tooltip>
                <TooltipTrigger>
                    <Badge variant="outline" className={statusColorMap[status]}>
                        {count}
                    </Badge>
                </TooltipTrigger>
                <TooltipContent>
                    <p>{tooltip}</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    );
}
