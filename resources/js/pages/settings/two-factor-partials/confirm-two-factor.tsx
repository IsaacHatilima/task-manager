import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/react';
import { FormEventHandler, useRef, useState } from 'react';

function ConfirmTwoFactor() {
    const [open, setOpen] = useState(false);
    const codeInput = useRef<HTMLInputElement>(null);
    const { data, setData, post, processing, reset, errors, setError, clearErrors } = useForm<
        Required<{
            code: string;
        }>
    >({ code: '' });

    const handleConfirm: FormEventHandler = (e) => {
        e.preventDefault();

        post('/user/confirmed-two-factor-authentication', {
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
                closeModal();
            },
            onError: () => {
                // Setting error to the code input because the error returned is not of the same format.
                setError({ code: 'Invalid code provided' });
                codeInput.current?.focus();
            },
            onFinish: () => reset(),
        });
    };

    const closeModal = () => {
        clearErrors();
        reset();
    };
    return (
        <div className="flex w-full items-center justify-center">
            <Dialog open={open} onOpenChange={setOpen}>
                <DialogTrigger asChild>
                    <Button>Confirm Two Factor Authentication</Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogTitle>Two Factor Authentication Confirmation</DialogTitle>
                    <DialogDescription>Enter the code from your authenticator app to confirm two factor authentication.</DialogDescription>
                    <form className="space-y-6" onSubmit={handleConfirm}>
                        <div className="grid gap-2">
                            <Label htmlFor="code" className="sr-only">
                                Code
                            </Label>

                            <Input
                                id="code"
                                type="number"
                                name="code"
                                ref={codeInput}
                                value={data.code}
                                onChange={(e) => setData('code', e.target.value)}
                                placeholder="Code"
                                autoComplete="code"
                            />

                            <InputError message={errors.code} />
                        </div>

                        <DialogFooter className="gap-2">
                            <DialogClose asChild>
                                <Button variant="secondary" onClick={closeModal}>
                                    Cancel
                                </Button>
                            </DialogClose>

                            <Button disabled={processing} asChild>
                                <button type="submit">Confirm 2FA</button>
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    );
}

export default ConfirmTwoFactor;
