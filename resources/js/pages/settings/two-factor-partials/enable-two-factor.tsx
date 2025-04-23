import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { useEffect } from 'react';

function EnableTwoFactor() {
    const handleActivate = (): void => {
        sessionStorage.setItem('shouldActivate2FA', 'true'); // flag it
        router.post('/user/two-factor-authentication'); // will trigger password confirm
    };

    useEffect(() => {
        if (sessionStorage.getItem('shouldActivate2FA') === 'true') {
            sessionStorage.removeItem('shouldActivate2FA'); // clean up
            router.post('/user/two-factor-authentication');
        }
    }, []);

    return (
        <div className="flex w-full items-center justify-center">
            <Button onClick={handleActivate}>Activate Two Factor Authentication</Button>
        </div>
    );
}

export default EnableTwoFactor;
