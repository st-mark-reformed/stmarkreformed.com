'use client';

import React, { Fragment, useState } from 'react';
import { Bars3Icon, XMarkIcon } from '@heroicons/react/16/solid';
import { Transition } from '@headlessui/react';
import Link from 'next/link';
import MenuMainDesktop from './MenuMainDesktop';
import MenuSecondaryDesktop from './MenuSecondaryDesktop';
import MenuMainMobile from './MenuMainMobile';
import MenuSecondaryMobile from './MenuSecondaryMobile';

export default function Nav () {
    const [mobileMenuIsActive, setMobileMenuIsActive] = useState(
        false,
    );

    return (
        <>
            <div
                className="pt-6"
                onBlur={() => setMobileMenuIsActive(false)}
            >
                <nav
                    className="relative max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6"
                    aria-label="Global"
                >
                    <div className="flex items-center flex-1">
                        <div className="flex items-center justify-between w-full md:w-auto">
                            <Link href="/">
                                <span className="sr-only">St. Mark Reformed Church</span>
                                <img
                                    className="h-8 w-auto sm:h-10"
                                    src="/images/logo/logo-website-header.png"
                                    alt=""
                                />
                            </Link>
                            <div className="-mr-2 flex items-center md:hidden">
                                <button
                                    type="button"
                                    className="bg-bronze rounded-md p-2 inline-flex items-center justify-center text-gray-200 hover:bg-saddle-brown focus:outline-none focus:ring-2 focus-ring-inset focus:ring-white"
                                    onClick={() => setMobileMenuIsActive(!mobileMenuIsActive)}
                                >
                                    <span className="sr-only">Open main menu</span>
                                    <Bars3Icon className="h-6 w-6" />
                                </button>
                            </div>
                        </div>
                        <div className="hidden space-x-8 md:flex md:ml-10">
                            <MenuMainDesktop />
                        </div>
                    </div>
                    <div className="hidden md:flex md:items-center md:space-x-6">
                        <MenuSecondaryDesktop />
                    </div>
                </nav>
            </div>
            <Transition
                as={Fragment}
                enter="transition ease-out duration-150"
                enterFrom="opacity-0 scale-95"
                enterTo="opacity-100 scale-100"
                leave="duration-100 ease-in"
                leaveFrom="opacity-100 scale-100"
                leaveTo="opacity-0 scale-95"
                show={mobileMenuIsActive}
            >
                <div className="absolute top-0 inset-x-0 p-2 transition transform origin-top md:hidden z-50 select-none">
                    <div className="rounded-lg shadow-md bg-white overflow-hidden">
                        <div className="px-5 pt-4 flex items-center justify-between">
                            <div>
                                <img className="h-8 w-auto" src="/images/logo/logo-website-header.png" alt="" />
                            </div>
                            <div className="-mr-2">
                                <button
                                    type="button"
                                    className="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-cyan-600"
                                >
                                    <span className="sr-only">Close menu</span>
                                    <XMarkIcon className="h-6 w-6" />
                                </button>
                            </div>
                        </div>
                        <div className="pt-5 pb-6">
                            <MenuMainMobile />
                            <MenuSecondaryMobile />
                        </div>
                    </div>
                </div>
            </Transition>
        </>
    );
}
