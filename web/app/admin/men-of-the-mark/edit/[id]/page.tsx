import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import GetEditMenOfTheMark from './GetEditMenOfTheMark';
import { createPageTitle } from '../../../../createPageTitle';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditMenOfTheMarkRoleGuard from '../../HasEditMenOfTheMarkRoleGuard/HasEditMenOfTheMarkRoleGuard';
import EditMenOfTheMarkPage from './EditMenOfTheMarkPage';

type Props = {
    params: Promise<{ id: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { id } = await params;

    const item = await GetEditMenOfTheMark(id);

    if (!item) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit Men of the Mark: ${item?.title}`,
            'Men of the Mark',
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { id } = await params;

    return (
        <AdminLayout activeNav="menOfTheMark">
            <HasEditMenOfTheMarkRoleGuard>
                <EditMenOfTheMarkPage id={id} />
            </HasEditMenOfTheMarkRoleGuard>
        </AdminLayout>
    );
}
