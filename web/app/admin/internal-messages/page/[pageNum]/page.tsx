import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import AdminLayout from '../../../Layout/AdminLayout';
import InternalMessagesPage from '../../InternalMessagesPage';
import HasEditMessagesRoleGuard from '../../../messages/HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import { createPageTitle } from '../../../../createPageTitle';

export async function generateMetadata (
    {
        params,
    }: {
        params: Promise<{
            pageNum: string;
        }>;
    },
): Promise<Metadata> {
    const { pageNum } = await params;

    return {
        title: createPageTitle([
            `Page ${pageNum}`,
            'Internal Messages',
            'Admin',
        ]),
    };
}

export default async function Page (
    {
        params,
    }: {
        params: Promise<{
            pageNum: string;
        }>;
    },
) {
    const { pageNum } = await params;

    const isNumeric = /^\d+$/.test(pageNum);

    if (!isNumeric) {
        notFound();
    }

    const pageNumInt = parseInt(pageNum, 10);

    if (pageNumInt < 2) {
        notFound();
    }

    return (
        <AdminLayout activeNav="internalMessages">
            <HasEditMessagesRoleGuard>
                <InternalMessagesPage pageNum={pageNumInt} />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
