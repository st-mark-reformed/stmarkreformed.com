import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../../Layout/AdminLayout';
import FailedQueuePage from './FailedQueuePage';
import { createPageTitle } from '../../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Failed',
        'Queue',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="queue">
            <FailedQueuePage />
        </AdminLayout>
    );
}
