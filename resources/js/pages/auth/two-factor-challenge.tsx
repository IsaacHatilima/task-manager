import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

export default function TwoFactorChallenge() {
    const { data, setData, post, processing, errors, reset } = useForm<Required<{ code: string }>>({
        code: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/two-factor-challenge', {
            onFinish: () => reset('code'),
        });
    };

    return (
        <AuthLayout title="Two Factor Authentication" description="Enter the code from your Two-Factor Authentication app.">
            <Head title="Two Factor Authentication" />

            <form onSubmit={submit}>
                <div className="space-y-6">
                    <div className="grid gap-2">
                        <Label htmlFor="code">Code</Label>
                        <Input
                            id="code"
                            type="text"
                            name="code"
                            placeholder="Code"
                            autoComplete="code"
                            value={data.code}
                            autoFocus
                            onChange={(e) => setData('code', e.target.value)}
                        />

                        <InputError message={errors.code} />
                    </div>

                    <div className="flex items-center">
                        <Button className="w-full" disabled={processing}>
                            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                            Confirm password
                        </Button>
                    </div>
                </div>
            </form>
        </AuthLayout>
    );
}
