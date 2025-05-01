import * as React from 'react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerFooter,
    DrawerHeader,
    DrawerTitle,
    DrawerTrigger,
} from '@/components/ui/drawer';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { User } from '@/types';
import { Task } from '@/types/task';
import { Todo } from '@/types/todo';
import { useMediaQuery } from '@custom-react-hooks/use-media-query';
import { useForm, usePage } from '@inertiajs/react';
import { toast } from 'sonner';

type TaskFormType = {
    title: string;
    description: string;
    status: string;
    assigned?: string;
};

type TodoFormProps = {
    onSuccess?: () => void;
    task?: Task;
    todo: Todo;
};

function TaskForm({ onSuccess, todo, task }: TodoFormProps) {
    const todoStatus: Array<string> = usePage().props.todoStatus as Array<string>;
    const todCollaborators = usePage().props.todCollaborators as User[];
    console.log(todCollaborators);
    const { data, setData, post, put, errors, processing } = useForm<Required<TaskFormType>>({
        title: task ? task.title : '',
        description: task ? task.description : '',
        status: task ? task.status : todoStatus[0],
        assigned: task?.assigned?.id ?? '',
    });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();

        if (task) {
            handleUpdateTask(todo);
        } else {
            handleCreateTask();
        }
    };

    const handleCreateTask = () => {
        post(route('todo.task.store', todo.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Success', {
                    description: 'Task created successfully',
                });

                if (onSuccess) onSuccess();
            },
        });
    };

    const handleUpdateTask = (todo: Todo) => {
        put(route('todo.task.update', [todo.id, task?.id]), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Success', {
                    description: 'Task updated successfully',
                });

                if (onSuccess) onSuccess();
            },
        });
    };

    return (
        <form onSubmit={handleSubmit} className="grid items-start gap-4">
            <div className="grid gap-2">
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
            <div className="grid gap-2">
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
            <div className="grid gap-2">
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

            <div className="grid gap-2">
                <Label htmlFor="assigned">Assign To</Label>

                <Select value={data.assigned} onValueChange={(value) => setData('assigned', value)}>
                    <SelectTrigger className="mt-1 w-full">
                        <SelectValue placeholder="Select Collaborator" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Collaborators</SelectLabel>
                            {todCollaborators.map((user: User) => (
                                <SelectItem key={user.id} value={user.id}>
                                    {user.profile.first_name} {user.profile.last_name}
                                </SelectItem>
                            ))}
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <InputError className="mt-2" message={errors.assigned} />
            </div>

            <Button disabled={processing}>Create</Button>
        </form>
    );
}

export default function CreateUpdateTask({ isMain, todo, task }: { isMain: boolean; todo: Todo; task?: Task }) {
    const [open, setOpen] = React.useState(false);
    const isDesktop = useMediaQuery('(min-width: 768px)');
    const heading = 'Task Manager';
    const subheading = 'Here you can manage your Task.';

    if (isDesktop) {
        return (
            <Dialog open={open} onOpenChange={setOpen}>
                <DialogTrigger asChild>
                    {isMain ? <Button size="default">Create Todo</Button> : <h1 className="cursor-pointer text-sky-700 hover:underline">Edit</h1>}
                </DialogTrigger>
                <DialogContent className="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>{heading}</DialogTitle>
                        <DialogDescription>{subheading}</DialogDescription>
                    </DialogHeader>
                    <TaskForm onSuccess={() => setOpen(false)} todo={todo} task={task} />
                </DialogContent>
            </Dialog>
        );
    }

    return (
        <Drawer open={open} onOpenChange={setOpen}>
            <DrawerTrigger asChild>
                <Button>Create Todo</Button>
            </DrawerTrigger>
            <DrawerContent>
                <DrawerHeader className="text-left">
                    <DrawerTitle>{heading}</DrawerTitle>
                    <DrawerDescription>{subheading}</DrawerDescription>
                </DrawerHeader>
                <TaskForm onSuccess={() => setOpen(false)} todo={todo} task={task} />
                <DrawerFooter className="pt-2">
                    <DrawerClose asChild>
                        <Button variant="outline">Cancel</Button>
                    </DrawerClose>
                </DrawerFooter>
            </DrawerContent>
        </Drawer>
    );
}
