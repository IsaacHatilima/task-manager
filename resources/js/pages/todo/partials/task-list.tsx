import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { PaginatedTask, Task, TaskStats } from '@/types/task';
import { Todo } from '@/types/todo';
import { Link, router } from '@inertiajs/react';

export default function TaskList({ todo, tasks, taskCounts }: { todo: Todo; tasks: PaginatedTask; taskCounts: TaskStats }) {
    const statusColorMap: Record<string, string> = {
        completed: 'border-green-500 text-green-600',
        cancelled: 'border-red-500 text-red-600',
        pending: 'border-amber-500 text-amber-600',
        in_progress: 'border-blue-500 text-blue-600',
    };

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
