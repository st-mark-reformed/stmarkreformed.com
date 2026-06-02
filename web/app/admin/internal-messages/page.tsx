import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import InternalMessagesPage from './InternalMessagesPage';
import HasEditMessagesRoleGuard from '../messages/HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Internal Messages',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="internalMessages">
            <HasEditMessagesRoleGuard>
                <InternalMessagesPage pageNum={1} />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
