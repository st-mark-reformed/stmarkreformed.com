import React from 'react';
import AdminLayout from '../Layout/AdminLayout';
import SchedulePage from './SchedulePage';

export default async function Page () {
    return (
        <AdminLayout activeNav="schedule">
            <SchedulePage />
        </AdminLayout>
    );
}
