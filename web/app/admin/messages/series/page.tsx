import React from 'react';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import SeriesPage from './SeriesPage';

export default async function Page () {
    return (
        <AdminLayout activeNav="messages">
            <HasEditMessagesRoleGuard>
                <SeriesPage />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
