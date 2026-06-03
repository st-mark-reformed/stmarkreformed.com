import React, { ReactNode } from 'react';
import GetHasEditMenOfTheMarkRole from './GetHasEditMenOfTheMarkRole';
import Alert from '../../../Alert';

export default async function HasEditMenOfTheMarkRoleGuard (
    {
        children,
    }: {
        children: ReactNode;
    },
) {
    const hasRole = await GetHasEditMenOfTheMarkRole();

    if (!hasRole) {
        return (
            <Alert
                headline="Access denied"
                content="You do not have access to edit Men of the Mark."
                type="error"
            />
        );
    }

    return <>{children}</>;
}
