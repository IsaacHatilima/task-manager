import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import InviteMember from '@/pages/todo/partials/invite-member';
import { BreadcrumbItem, PaginatedUsers, type SharedData, User, UserFilters } from '@/types';
import { Todo } from '@/types/todo';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import { debounce } from 'lodash';
import { useEffect, useMemo } from 'react';
import { toast } from 'sonner';

function Collaborators() {
    const { auth } = usePage<SharedData>().props;
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
            href: route('todos.collaborators.index', todo.id),
        },
    ];

    const filters: UserFilters = usePage().props.filters as UserFilters;
    const { data, setData } = useForm({
        email: filters?.email || '',
    });

    const debouncedSearch = useMemo(() => {
        return debounce(() => {
            const filtersApplied = Object.keys(data).some((key) => data[key as keyof UserFilters] !== '' && data[key as keyof UserFilters] !== null);

            const params: Record<string, string | number> = {
                ...data,
                page: filtersApplied ? 1 : todoMembers.current_page,
            };

            Object.keys(params).forEach((key) => {
                if (params[key] === '' || params[key] === null || params[key] === 'all') {
                    delete params[key];
                }
            });

            router.get(route('todos.collaborators.index', todo.id), params, {
                preserveState: true,
                preserveScroll: true,
            });
        }, 300);
    }, [data, todoMembers.current_page, todo]);

    useEffect(() => {
        debouncedSearch();
        return () => {
            debouncedSearch.cancel();
        };
    }, [data, debouncedSearch]);

    const handleDeleteTodoMember = (userId: string) => {
        router.delete(route('todos.collaborators.destroy', { todo: todo.id, user: userId }), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Success', {
                    description: 'Todo member deleted successfully',
                });
            },
        });
    };

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
                            <div className="mb-4 w-96">
                                <Input
                                    className="font-medium"
                                    id="email"
                                    name="email"
                                    type="email"
                                    placeholder="Search Email"
                                    value={data.email}
                                    onChange={(e) => {
                                        setData('email', e.target.value);
                                    }}
                                />
                            </div>
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
                                            <TableCell>
                                                {auth.user.id === todo.user.id && user.id !== todo.user.id && (
                                                    <Dialog>
                                                        <DialogTrigger asChild>
                                                            <span className="cursor-pointer text-red-500 hover:text-red-700 hover:underline">
                                                                Delete
                                                            </span>
                                                        </DialogTrigger>
                                                        <DialogContent className="sm:max-w-md">
                                                            <DialogHeader>
                                                                <DialogTitle>Remove Collaborator?</DialogTitle>
                                                                <DialogDescription>
                                                                    This action cannot be undone. Are you sure you want to remove this collaborator
                                                                    from the Todo?
                                                                </DialogDescription>
                                                            </DialogHeader>
                                                            <DialogFooter>
                                                                <Button variant="destructive" onClick={() => handleDeleteTodoMember(user.id)}>
                                                                    Delete
                                                                </Button>
                                                                <DialogClose asChild>
                                                                    <Button type="button" variant="secondary">
                                                                        Close
                                                                    </Button>
                                                                </DialogClose>
                                                            </DialogFooter>
                                                        </DialogContent>
                                                    </Dialog>
                                                )}
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
