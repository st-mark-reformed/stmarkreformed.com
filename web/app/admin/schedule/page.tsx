import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import SchedulePage from './SchedulePage';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Schedule',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="schedule">
            <SchedulePage />
        </AdminLayout>
    );
}
