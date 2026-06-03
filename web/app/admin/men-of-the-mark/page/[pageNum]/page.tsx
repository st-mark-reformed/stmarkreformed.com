import React from 'react';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import AdminLayout from '../../../Layout/AdminLayout';
import MenOfTheMarkPage from '../../MenOfTheMarkPage';
import HasEditMenOfTheMarkRoleGuard from '../../HasEditMenOfTheMarkRoleGuard/HasEditMenOfTheMarkRoleGuard';
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
            'Men of the Mark',
            'Admin',
        ]),
    };
}

export default async function Page (
    {
        params,
        searchParams,
    }: {
        params: Promise<{
            pageNum: string;
        }>;
        searchParams: Promise<{
            keyword?: string;
        }>;
    },
) {
    const { pageNum } = await params;

    const { keyword } = await searchParams;

    const isNumeric = /^\d+$/.test(pageNum);

    if (!isNumeric) {
        notFound();
    }

    const pageNumInt = parseInt(pageNum, 10);

    if (pageNumInt < 2) {
        notFound();
    }

    return (
        <AdminLayout activeNav="menOfTheMark">
            <HasEditMenOfTheMarkRoleGuard>
                <MenOfTheMarkPage pageNum={pageNumInt} keyword={keyword ?? ''} />
            </HasEditMenOfTheMarkRoleGuard>
        </AdminLayout>
    );
}
