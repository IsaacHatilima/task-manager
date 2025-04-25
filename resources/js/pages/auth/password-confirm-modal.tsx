import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler, useRef } from 'react';

type ButtonVariant = 'default' | 'outline' | 'secondary' | 'destructive' | 'link' | 'ghost';

interface PasswordConfirmModalProps {
    btnLabel: string;
    btnVariant?: ButtonVariant;
    intendedCall: () => void; // or FormEventHandler if it's for forms
}

export function PasswordConfirmModal({ btnLabel, btnVariant = 'outline', intendedCall }: PasswordConfirmModalProps) {
    const { data, setData, post, processing, errors } = useForm<Required<{ password: string }>>({
        password: '',
    });

    const passwordFormRef = useRef<HTMLFormElement>(null);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('password.confirmation'), {
            onSuccess: () => {
                intendedCall();
            },
        });
    };
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant={btnVariant}>{btnLabel}</Button>
            </DialogTrigger>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Password Confirmation</DialogTitle>
                    <DialogDescription>Enter your password to proceed.</DialogDescription>
                </DialogHeader>
                <div className="grid gap-4 py-4">
                    <form ref={passwordFormRef} onSubmit={submit}>
                        <div className="space-y-6">
                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Password"
                                    autoComplete="current-password"
                                    value={data.password}
                                    autoFocus
                                    onChange={(e) => setData('password', e.target.value)}
                                />

                                <InputError message={errors.password} />
                            </div>

                            <div className="flex items-center">
                                <Button className="w-full" disabled={processing}>
                                    {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                    Confirm password
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>
            </DialogContent>
        </Dialog>
    );
}
