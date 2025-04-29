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
import { Todo } from '@/types/todo';
import { useMediaQuery } from '@custom-react-hooks/use-media-query';
import { useForm } from '@inertiajs/react';
import React, { FormEventHandler } from 'react';
import { toast } from 'sonner';

type TodoFormType = {
    email: string;
};

type TodoFormProps = {
    onSuccess?: () => void;
    todo: Todo;
};

function TodoForm({ onSuccess, todo }: TodoFormProps) {
    const { data, setData, post, errors, processing } = useForm<Required<TodoFormType>>({
        email: '',
    });

    const handleInvite: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('todos.collaborators.store', todo.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Success', {
                    description: 'Todo invite sent successfully',
                });

                if (onSuccess) onSuccess();
            },
        });
    };

    return (
        <form onSubmit={handleInvite} className="grid items-start gap-4">
            <div className="grid gap-2">
                <Label htmlFor="email">E-Mail</Label>

                <Input
                    id="email"
                    type="text"
                    className="mt-1 block w-full"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    required
                    autoComplete="email"
                    placeholder="user@example.com"
                />

                <InputError className="mt-2" message={errors.email} />
            </div>

            <Button disabled={processing}>Create</Button>
        </form>
    );
}

function InviteMember({ todo }: { todo: Todo }) {
    const [open, setOpen] = React.useState(false);
    const isDesktop = useMediaQuery('(min-width: 768px)');
    const title = 'Invite Members To Todo';
    const description = 'Send an invite to collaborators.';

    if (isDesktop) {
        return (
            <Dialog open={open} onOpenChange={setOpen}>
                <DialogTrigger asChild>
                    <Button size="default">Invite Member</Button>
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
                <Button>Invite Member</Button>
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

export default InviteMember;
