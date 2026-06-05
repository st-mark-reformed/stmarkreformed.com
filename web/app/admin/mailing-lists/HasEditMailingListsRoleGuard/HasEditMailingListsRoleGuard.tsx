import React, { ReactNode } from 'react';
import GetHasEditMailingListsRole from './GetHasEditMailingListsRole';
import Alert from '../../../Alert';

export default async function HasEditMailingListsRoleGuard (
    {
        children,
    }: {
        children: ReactNode;
    },
) {
    const hasEditMailingListsRole = await GetHasEditMailingListsRole();

    if (!hasEditMailingListsRole) {
        return (
            <Alert
                headline="Access denied"
                content="You do not have access to edit mailing lists."
                type="error"
            />
        );
    }

    return <>{children}</>;
}
