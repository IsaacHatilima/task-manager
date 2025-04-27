import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { useEffect } from 'react';

function EnableTwoFactor() {
    const handleActivate = (): void => {
        /**
         * Laravel password confirm returns to initial route for resubmition
         * a seesion value is set to auto trigger the resubmit to activate 2FA
         */
        sessionStorage.setItem('shouldActivate2FA', 'true'); // Flag it
        router.post('/user/two-factor-authentication'); // Will trigger password confirm
    };

    useEffect(() => {
        if (sessionStorage.getItem('shouldActivate2FA') === 'true') {
            sessionStorage.removeItem('shouldActivate2FA'); // Clean up
            router.post('/user/two-factor-authentication'); // Activate 2FA
        }
    }, []);

    return (
        <div className="flex w-full items-center justify-center">
            <Button onClick={handleActivate}>Activate Two Factor Authentication</Button>
        </div>
    );
}

export default EnableTwoFactor;
