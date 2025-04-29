import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import InviteMember from '@/pages/todo/partials/invite-member';
import { BreadcrumbItem, PaginatedUsers, User } from '@/types';
import { Todo } from '@/types/todo';
import { Head, router, usePage } from '@inertiajs/react';

function Collaborators() {
    const todo: Todo = usePage().props.todo as Todo;
    const todoMembers: PaginatedUsers = usePage().props.todoMembers as PaginatedUsers;
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Todos',
            href: route('todos.index'),
        },
        {
            title: todo.title,
            href: route('todos.show', todo.id),
        },
        {
            title: 'Todo Members',
            href: route('todos.members.index', todo.id),
        },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Todo Collaborators" />
            <div className="flex justify-center">
                <div className="w-full">
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle>{todo.title}</CardTitle>
                                <InviteMember todo={todo} />
                            </div>
                            <CardDescription>Todo Collaborators</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableCaption>A list of Todo Collaborators.</TableCaption>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Action</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {todoMembers.data.map((user: User) => (
                                        <TableRow key={user.id}>
                                            <TableCell className="font-medium">
                                                {user.profile.first_name} {user.profile.last_name}
                                            </TableCell>
                                            <TableCell>{user.email}</TableCell>
                                            <TableCell>Delete</TableCell>
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
                                                if (todoMembers.prev_page_url) {
                                                    router.visit(todoMembers.prev_page_url);
                                                }
                                            }}
                                        />
                                    </PaginationItem>

                                    {todoMembers.links.map((page, index) => {
                                        if (index === 0 || index === todoMembers.links.length - 1) {
                                            return null; // Skip default "Previous" and "Next"
                                        }

                                        const currentPage = todoMembers.current_page;
                                        const totalPages = todoMembers.last_page;

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
                                                if (todoMembers.next_page_url) {
                                                    router.visit(todoMembers.next_page_url);
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

export default Collaborators;
