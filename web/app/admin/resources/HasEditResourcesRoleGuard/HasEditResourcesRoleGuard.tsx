import React, { ReactNode } from 'react';
import GetHasEditResourcesRole from './GetHasEditResourcesRole';
import Alert from '../../../Alert';

export default async function HasEditResourcesRoleGuard (
    {
        children,
    }: {
        children: ReactNode;
    },
) {
    const hasEditResourcesRole = await GetHasEditResourcesRole();

    if (!hasEditResourcesRole) {
        return (
            <Alert
                headline="Access denied"
                content="You do not have access to edit resources."
                type="error"
            />
        );
    }

    return <>{children}</>;
}
