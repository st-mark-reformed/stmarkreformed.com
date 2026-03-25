import React from 'react';
import AdminLayout from '../Layout/AdminLayout';
import MessagesPage from './MessagesPage';
import HasEditMessagesRoleGuard from './HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';

export default async function Page () {
    return (
        <AdminLayout activeNav="messages">
            <HasEditMessagesRoleGuard>
                <MessagesPage />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
