import React from 'react';
import AdminLayout from '../../Layout/AdminLayout';
import FailedQueuePage from './FailedQueuePage';

export default async function Page () {
    return (
        <AdminLayout activeNav="queue">
            <FailedQueuePage />
        </AdminLayout>
    );
}
