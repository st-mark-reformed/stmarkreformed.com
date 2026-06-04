import React, { ReactNode } from 'react';
import GetHasEditHymnsOfTheMonthRole from './GetHasEditHymnsOfTheMonthRole';
import Alert from '../../../Alert';

export default async function HasEditHymnsOfTheMonthRoleGuard (
    {
        children,
    }: {
        children: ReactNode;
    },
) {
    const hasEditHymnsOfTheMonthRole = await GetHasEditHymnsOfTheMonthRole();

    if (!hasEditHymnsOfTheMonthRole) {
        return (
            <Alert
                headline="Access denied"
                content="You do not have access to edit the Hymns of the Month."
                type="error"
            />
        );
    }

    return <>{children}</>;
}
