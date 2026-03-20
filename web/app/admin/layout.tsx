import React, { ReactNode } from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../createPageTitle';
import Sidebar from './Layout/Sidebar';

export const metadata: Metadata = {
    title: createPageTitle('Admin'),
};

/**
 * Reading env/secrets config requires loading dynamically at runtime rather
 * than build time. This ensures that all server components render dynamically
 * because this is our root layout.
 * @see https://nextjs.org/docs/app/api-reference/file-conventions/route-segment-config
 */
export const dynamic = 'force-dynamic';

export default async function AdminLayout (
    { children }: { children: ReactNode },
) {
    return (
        <>
            <div>
                <Sidebar activeNav="Messages" />
                <main className="py-10 lg:pl-72">
                    <div className="px-4 sm:px-6 lg:px-8">
                        {children}
                    </div>
                </main>
            </div>
        </>
    );
}
