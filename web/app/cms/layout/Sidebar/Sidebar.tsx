'use client';

import React, { Fragment, useState } from 'react';
import { Bars3Icon } from '@heroicons/react/24/outline';
import SidebarMobile from './SidebarMobile';
import Navigation from '../Navigation';
import SidebarDesktop from './SidebarDesktop';

export default function Sidebar () {
    const navigation = Navigation();

    const [sidebarOpen, setSidebarOpen] = useState(false);

    return (
        <>
            <SidebarMobile
                sidebarOpen={sidebarOpen}
                setSidebarOpen={setSidebarOpen}
                navigation={navigation}
            />
            <SidebarDesktop
                navigation={navigation}
            />
            <div className="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 lg:hidden">
                <button
                    type="button"
                    className="-m-2.5 p-2.5 text-gray-700 lg:hidden cursor-pointer"
                    onClick={() => setSidebarOpen(true)}
                >
                    <span className="sr-only">Open sidebar</span>
                    <Bars3Icon className="h-6 w-6" aria-hidden="true" />
                </button>
            </div>
        </>
    );
}
