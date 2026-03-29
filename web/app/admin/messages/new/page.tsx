import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../createPageTitle';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import CreateNewMessagePage from './CreateNewMessagePage';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        'Messages',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="messages">
            <HasEditMessagesRoleGuard>
                <CreateNewMessagePage />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
