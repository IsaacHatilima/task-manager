import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { PasswordConfirmModal } from '@/pages/auth/password-confirm-modal';
import TaskList from '@/pages/todo/partials/task-list';
import type { BreadcrumbItem, SharedData } from '@/types';
import { TaskStats } from '@/types/task';
import { Todo } from '@/types/todo';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';
import { toast } from 'sonner';

type TodoFormType = {
    title: string;
    description: string;
    status: string;
};

function TodoDetails() {
    const todo: Todo = usePage().props.todo as Todo;

    const taskCounts: TaskStats = usePage().props.taskCounts as TaskStats;
    const { auth } = usePage<SharedData>().props;

    const todoStatus: Array<string> = usePage().props.todoStatus as Array<string>;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Todos',
            href: route('todos.index'),
        },
        {
            title: 'Todo Details',
            href: route('todos.show', todo.id),
        },
    ];

    const statusColorMap: Record<string, string> = {
        completed: 'border-green-500 text-green-600',
        cancelled: 'border-red-500 text-red-600',
        pending: 'border-amber-500 text-amber-600',
        in_progress: 'border-blue-500 text-blue-600',
    };

    const {
        data,
        setData,
        put,
        delete: destroy,
        errors,
        processing,
    } = useForm<Required<TodoFormType>>({
        title: todo ? todo.title : '',
        description: todo ? todo.description : '',
        status: todo ? todo.status : todoStatus[0],
    });

    const handleUpdateTodo: FormEventHandler = (e) => {
        e.preventDefault();

        put(route('todos.update', todo.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Success', {
                    description: 'Todo updated successfully',
                });
            },
        });
    };

    const handleDeleteTodo = () => {
        destroy(route('todos.destroy', todo.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Success', {
                    description: 'Todo Deleted successfully',
                });
            },
            onError: () => {
                toast.warning('Warning', { description: 'Password confirmation expired. Try again' });
            },
            onCancel: () => {},
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Todo Details" />
            <div className="mb-2 flex justify-end">
                <Button
                    type="button"
                    onClick={() => {
                        router.visit(route('todos.collaborators.index', todo.id));
                    }}
                >
                    Todo Member
                </Button>
            </div>
            <div className="grid grid-cols-1 gap-4 md:min-h-[650px] md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center justify-between">
                            <div>
                                {todo.title} /{' '}
                                <Badge variant="outline" className={statusColorMap[todo.status]}>
                                    {todo.status
                                        .split('_')
                                        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                                        .join(' ')}
                                </Badge>
                            </div>
                            {auth.user.id === todo.user.id && (
                                <PasswordConfirmModal btnLabel="Delete" btnVariant="destructive" intendedCall={handleDeleteTodo} />
                            )}
                        </CardTitle>
                        <CardDescription>
                            <strong>Created At:</strong> {new Date(todo.created_at).toLocaleDateString()}
                        </CardDescription>
                    </CardHeader>
                    <form onSubmit={handleUpdateTodo} className="grid items-start gap-4">
                        <CardContent>
                            <div className="mt-4 grid gap-2">
                                <Label htmlFor="title">Title</Label>

                                <Input
                                    id="title"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    required
                                    autoComplete="title"
                                    placeholder="Title"
                                />

                                <InputError className="mt-2" message={errors.title} />
                            </div>
                            <div className="mt-4 grid gap-2">
                                <Label htmlFor="description">Description</Label>

                                <Textarea
                                    id="description"
                                    className="mt-1 block w-full"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    required
                                    autoComplete="description"
                                    placeholder="Description"
                                />

                                <InputError className="mt-2" message={errors.description} />
                            </div>
                            <div className="mt-4 grid gap-2">
                                <Label htmlFor="status">Status</Label>

                                <Select value={data.status} onValueChange={(value) => setData('status', value)}>
                                    <SelectTrigger className="mt-1 w-full">
                                        <SelectValue placeholder="Select Status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Status</SelectLabel>
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

                                <InputError className="mt-2" message={errors.status} />
                            </div>
                        </CardContent>
                        <CardFooter className="flex justify-end gap-3">
                            <Button disabled={processing}>{processing && <LoaderCircle className="h-4 w-4 animate-spin" />}Update</Button>
                        </CardFooter>
                    </form>
                </Card>

                <TaskList todo={todo} taskCounts={taskCounts} todoStatus={todoStatus} />
            </div>
        </AppLayout>
    );
}

export default TodoDetails;
