import React, { ReactNode } from 'react';
import GetHasEditNewsRole from './GetHasEditNewsRole';
import Alert from '../../../Alert';

export default async function HasEditNewsRoleGuard (
    {
        children,
    }: {
        children: ReactNode;
    },
) {
    const hasEditNewsRole = await GetHasEditNewsRole();

    if (!hasEditNewsRole) {
        return (
            <Alert
                headline="Access denied"
                content="You do not have access to edit news."
                type="error"
            />
        );
    }

    return <>{children}</>;
}
