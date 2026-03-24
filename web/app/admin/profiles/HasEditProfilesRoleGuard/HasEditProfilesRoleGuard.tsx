import React, { ReactNode } from 'react';
import GetHasEditProfilesRole from './GetHasEditProfilesRole';
import Alert from '../../../Alert';

export default async function HasEditProfilesRoleGuard (
    {
        children,
    }: {
        children: ReactNode;
    },
) {
    const hasEditProfilesRole = await GetHasEditProfilesRole();

    if (!hasEditProfilesRole) {
        return (
            <Alert
                headline="Access denied"
                content="You do not have access to edit profiles."
                type="error"
            />
        );
    }

    return <>{children}</>;
}
