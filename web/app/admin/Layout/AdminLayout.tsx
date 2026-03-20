import React, { ReactNode } from 'react';
import Sidebar from './Sidebar';

export default async function AdminLayout (
    {
        children,
        activeNav = null,
    }: {
        children: ReactNode;
        activeNav: null | 'messages' | 'profiles';
    },
) {
    return (
        <>
            <div>
                <Sidebar activeNav={activeNav} />
                <main className="py-10 lg:pl-72">
                    <div className="px-4 sm:px-6 lg:px-8">
                        {children}
                    </div>
                </main>
            </div>
        </>
    );
}
