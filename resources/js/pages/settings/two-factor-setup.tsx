import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import ConfirmTwoFactor from '@/pages/settings/two-factor-partials/confirm-two-factor';
import DisableTwoFactor from '@/pages/settings/two-factor-partials/disable-two-factor';
import EnableTwoFactor from '@/pages/settings/two-factor-partials/enable-two-factor';
import QrCode from '@/pages/settings/two-factor-partials/qr-code';
import RecoveryCodes from '@/pages/settings/two-factor-partials/recovery-codes';
import type { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Two-Factor Authentication',
        href: route('two-factor-authentication.edit'),
    },
];

function TwoFactorSetup() {
    const { auth } = usePage<SharedData>().props;
    const twoFactorEnabled: boolean = usePage().props.twoFactorEnabled as boolean;
    const setupCode: string = usePage().props.setupCode as string;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Two-Factor Authentication" />

            <SettingsLayout>
                {twoFactorEnabled ? (
                    <div className="flex w-full flex-col items-center justify-center">
                        <QrCode twoFactorEnabled={twoFactorEnabled} setupCode={setupCode} />

                        {auth.user.two_factor_confirmed_at && <RecoveryCodes downloadedCode={auth.user.downloaded_codes} />}

                        <div className="flex w-1/2 flex-col items-center justify-center gap-2 md:flex-row md:gap-0">
                            <DisableTwoFactor />
                            {!auth.user.two_factor_confirmed_at && <ConfirmTwoFactor />}
                        </div>
                    </div>
                ) : (
                    <EnableTwoFactor />
                )}
            </SettingsLayout>
        </AppLayout>
    );
}

export default TwoFactorSetup;
