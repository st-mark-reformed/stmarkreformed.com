import React from 'react';
import AdminLayout from '../Layout/AdminLayout';
import QueuePage from './QueuePage';

export default async function Page () {
    return (
        <AdminLayout activeNav="queue">
            <QueuePage />
        </AdminLayout>
    );
}
