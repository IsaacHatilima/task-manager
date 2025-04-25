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
import { Todo } from '@/types/todo';
import { useMediaQuery } from '@custom-react-hooks/use-media-query';
import { useForm, usePage } from '@inertiajs/react';
import { toast } from 'sonner';

type TodoFormType = {
    title: string;
    description: string;
    status: string;
};

type TodoFormProps = {
    onSuccess?: () => void;
    todo?: Todo;
};

function TodoForm({ onSuccess, todo }: TodoFormProps) {
    const todoStatus: Array<string> = usePage().props.todoStatus as Array<string>;
    const { data, setData, post, put, errors, processing } = useForm<Required<TodoFormType>>({
        title: todo ? todo.title : '',
        description: todo ? todo.description : '',
        status: todo ? todo.status : todoStatus[0],
    });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();

        if (todo) {
            handleUpdateTodo(todo);
        } else {
            handleCreateTodo();
        }
    };

    const handleCreateTodo = () => {
        post(route('todos.store'), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Success', {
                    description: 'Todo created successfully',
                });

                if (onSuccess) onSuccess();
            },
        });
    };

    const handleUpdateTodo = (todo: Todo) => {
        put(route('todos.update', todo.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Success', {
                    description: 'Todo updated successfully',
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

            <Button disabled={processing}>Create</Button>
        </form>
    );
}

function Create({ isMain, todo }: { isMain: boolean; todo?: Todo }) {
    const [open, setOpen] = React.useState(false);
    const isDesktop = useMediaQuery('(min-width: 768px)');
    const title = 'Create Todo';
    const description = "Create a new Todo here. Click create when you're done.";

    if (isDesktop) {
        return (
            <Dialog open={open} onOpenChange={setOpen}>
                <DialogTrigger asChild>
                    {isMain ? <Button size="default">Create Todo</Button> : <h1 className="cursor-pointer text-sky-700 hover:underline">Edit</h1>}
                </DialogTrigger>
                <DialogContent className="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>{title}</DialogTitle>
                        <DialogDescription>{description}</DialogDescription>
                    </DialogHeader>
                    <TodoForm onSuccess={() => setOpen(false)} todo={todo} />
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
                    <DrawerTitle>{title}</DrawerTitle>
                    <DrawerDescription>{description}</DrawerDescription>
                </DrawerHeader>
                <TodoForm onSuccess={() => setOpen(false)} todo={todo} />
                <DrawerFooter className="pt-2">
                    <DrawerClose asChild>
                        <Button variant="outline">Cancel</Button>
                    </DrawerClose>
                </DrawerFooter>
            </DrawerContent>
        </Drawer>
    );
}

export default Create;
