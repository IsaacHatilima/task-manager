import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import Create from '@/pages/todo/partials/create';
import { type BreadcrumbItem } from '@/types';
import { PaginatedTodos, Todo } from '@/types/todo';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Todos',
        href: route('todos.index'),
    },
];

export default function Index() {
    const todos: PaginatedTodos = usePage().props.todos as PaginatedTodos;
    const statusColorMap: Record<string, string> = {
        completed: 'border-green-500 text-green-600',
        canceled: 'border-red-500 text-red-600',
        pending: 'border-amber-500 text-amber-600',
        in_progress: 'border-blue-500 text-blue-600',
    };
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex justify-center p-4">
                <div className="w-1/2">
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
                                <TableCaption>A list of your recent invoices.</TableCaption>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead className="w-[100px]">Title</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Description</TableHead>
                                        <TableHead className="ml-4 text-right">Action</TableHead>
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
                                                <Create isMain={false} />
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                        <CardFooter>
                            <Pagination>
                                <PaginationContent>
                                    <PaginationItem>
                                        <PaginationPrevious href="#" />
                                    </PaginationItem>
                                    <PaginationItem>
                                        <PaginationLink href="#">1</PaginationLink>
                                    </PaginationItem>
                                    <PaginationItem>
                                        <PaginationEllipsis />
                                    </PaginationItem>
                                    <PaginationItem>
                                        <PaginationNext href="#" />
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
