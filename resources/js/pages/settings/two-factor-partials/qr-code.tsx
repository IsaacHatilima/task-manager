import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import axios from 'axios';
import { useEffect, useState } from 'react';

function QrCode({ twoFactorEnabled, setupCode }: { twoFactorEnabled: boolean; setupCode: string }) {
    const { auth } = usePage<SharedData>().props;
    const [qrCodeSvg, setQrCodeSvg] = useState<string | null>(null);

    /**
     * Fetches the QR code SVG for two-factor authentication.
     * This function is called when the component mounts or when the `twoFactorEnabled` prop changes.
     * It uses Axios to make a GET request to the server endpoint '/user/two-factor-qr-code' because inertia response
     * does not handle json responses.
     * */
    const handleGetTwoFactorQRCode = () => {
        axios.get('/user/two-factor-qr-code').then((response) => {
            setQrCodeSvg(response.data.svg);
        });
    };

    useEffect(() => {
        if (twoFactorEnabled) {
            handleGetTwoFactorQRCode();
        }
    }, [twoFactorEnabled]);

    return (
        <div>
            {qrCodeSvg && !auth.user.two_factor_confirmed_at && (
                <div className="mb-4 flex flex-col items-center justify-center">
                    <div dangerouslySetInnerHTML={{ __html: qrCodeSvg }} className="qr-code my-10 flex items-center justify-center" />
                    <div className="bg-muted rounded-lg p-2">
                        <h1 className="text-sm font-bold text-black">Setup Code: {setupCode}</h1>
                    </div>
                </div>
            )}
        </div>
    );
}

export default QrCode;
