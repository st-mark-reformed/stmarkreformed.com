import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import QueuePage from './QueuePage';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Queue',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="queue">
            <QueuePage />
        </AdminLayout>
    );
}
