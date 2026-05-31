import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import MessagesPage from './MessagesPage';
import HasEditMessagesRoleGuard from './HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Messages',
        'Admin',
    ]),
};

export default async function Page (
    {
        searchParams,
    }: {
        searchParams: Promise<{
            keyword?: string;
        }>;
    },
) {
    const { keyword } = await searchParams;

    return (
        <AdminLayout activeNav="messages">
            <HasEditMessagesRoleGuard>
                <MessagesPage pageNum={1} keyword={keyword ?? ''} />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
