import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { useEffect } from 'react';

function DisableTwoFactor() {
    const handleDeactivate = (): void => {
        sessionStorage.setItem('shouldDeactivate2FA', 'true'); // flag it
        router.delete('/user/two-factor-authentication'); // will trigger password confirm
    };

    useEffect(() => {
        if (sessionStorage.getItem('shouldDeactivate2FA') === 'true') {
            sessionStorage.removeItem('shouldDeactivate2FA'); // clean up
            router.delete('/user/two-factor-authentication');
        }
    }, []);
    return (
        <div className="flex w-full items-center justify-center">
            <Button onClick={handleDeactivate} variant="destructive">
                Disable Two Factor Authentication
            </Button>
        </div>
    );
}

export default DisableTwoFactor;
