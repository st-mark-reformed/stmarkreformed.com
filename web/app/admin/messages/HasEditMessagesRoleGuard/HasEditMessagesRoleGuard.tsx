import React, { ReactNode } from 'react';
import GetHasEditMessagesRole from './GetHasEditMessagesRole';
import Alert from '../../../Alert';

export default async function HasEditMessagesRoleGuard (
    {
        children,
    }: {
        children: ReactNode;
    },
) {
    const hasEditMessagesRole = await GetHasEditMessagesRole();

    if (!hasEditMessagesRole) {
        return (
            <Alert
                headline="Access denied"
                content="You do not have access to edit messages."
                type="error"
            />
        );
    }

    return <>{children}</>;
}
