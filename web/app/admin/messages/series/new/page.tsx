import React from 'react';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../../HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import CreateNewSeriesPage from './CreateNewSeriesPage';

export default async function Page () {
    return (
        <AdminLayout activeNav="messages">
            <HasEditMessagesRoleGuard>
                <CreateNewSeriesPage />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
