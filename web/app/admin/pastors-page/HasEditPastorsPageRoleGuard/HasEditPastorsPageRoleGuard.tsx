import React, { ReactNode } from 'react';
import GetHasEditPastorsPageRole from './GetHasEditPastorsPageRole';
import Alert from '../../../Alert';

export default async function HasEditPastorsPageRoleGuard (
    {
        children,
    }: {
        children: ReactNode;
    },
) {
    const hasEditPastorsPageRole = await GetHasEditPastorsPageRole();

    if (!hasEditPastorsPageRole) {
        return (
            <Alert
                headline="Access denied"
                content="You do not have access to edit the Pastor's Page."
                type="error"
            />
        );
    }

    return <>{children}</>;
}
