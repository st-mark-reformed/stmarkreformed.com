import React, { ReactNode, Suspense } from 'react';
import Sidebar from './Sidebar';
import PartialPageLoading from '../../PartialPageLoading';
import RequestFactory from '../../api/request/RequestFactory';
import KeepAlive from '../../KeepAlive';

export default async function AdminLayout (
    {
        children,
        activeNav = null,
    }: {
        children: ReactNode;
        activeNav: null | 'messages' | 'profiles' | 'queue' | 'schedule';
    },
) {
    await RequestFactory().makeWithSignInRedirect({
        uri: '/keep-alive',
        cacheSeconds: 0,
    });

    return (
        <>
            <div className="min-h-full bg-gray-50 dark:bg-gray-900">
                <Sidebar activeNav={activeNav} />
                <main className="py-10 lg:pl-72">
                    <div className="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                        <Suspense fallback={<PartialPageLoading />}>
                            {children}
                        </Suspense>
                    </div>
                </main>
            </div>
            <KeepAlive />
        </>
    );
}
