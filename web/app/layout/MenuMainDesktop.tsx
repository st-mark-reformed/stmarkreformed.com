'use client';

import React, { Fragment } from 'react';
import { Transition } from '@headlessui/react';
import Link from 'next/link';
import { ChevronDownIcon } from '@heroicons/react/16/solid';
import { MainMenu, MenuItems } from './MainMenu';

export default function MenuMainDesktop () {
    const [activeSubMenu, setActiveSubMenu] = React.useState('');

    return (
        <>
            {MainMenu.map((menuItem) => {
                if (menuItem.children.length > 0) {
                    const isActive = activeSubMenu === menuItem.name;

                    const classes = ['text-white group rounded-md inline-flex items-center text-base focus:outline-none cursor-pointer'];

                    if (menuItem.isEmphasized) {
                        classes.push('bg-crimson hover:bg-crimson-dark font-bold px-3');
                    } else {
                        classes.push('bg-transparent hover:text-goldenrod font-normal');
                    }

                    return (
                        <div
                            key={menuItem.link}
                            className="relative"
                            onBlur={() => setActiveSubMenu('')}
                        >
                            {/* @click="subMenuIsActive = !subMenuIsActive" */}
                            {/* x-bind:aria-expanded="subMenuIsActive" */}
                            <button
                                type="button"
                                className={classes.join(' ')}
                                onClick={() => setActiveSubMenu(
                                    isActive ? '' : menuItem.name,
                                )}
                            >
                                <span>{menuItem.name}</span>
                                <ChevronDownIcon className="ml-2 h-5 w-5" />
                            </button>
                            <Transition
                                as={Fragment}
                                enter="transition ease-out duration-150"
                                enterFrom="opacity-0 translate-y-1"
                                enterTo="opacity-100 translate-y-0"
                                leave="duration-100 ease-in"
                                leaveFrom="opacity-100 translate-y-0"
                                leaveTo="opacity-0 translate-y-1"
                                show={isActive}
                            >
                                <div className="absolute -ml-4 mt-3 transform w-screen max-w-xs -translate-x-1/2 left-1/2 rounded-lg shadow-lg z-50">
                                    <div className="rounded-lg shadow-lg ring-1 ring-gray-300 ring-opacity-5 overflow-hidden">
                                        <div className="relative grid gap-6 bg-white p-6">
                                            {(() => {
                                                let subMenuItems = [] as MenuItems;

                                                if (menuItem.link !== '') {
                                                    subMenuItems = [menuItem];
                                                }

                                                subMenuItems = subMenuItems.concat(menuItem.children);

                                                return (
                                                    <>
                                                        {subMenuItems.map((subMenuItem) => (
                                                            <Link
                                                                key={subMenuItem.link}
                                                                href={subMenuItem.link}
                                                                className="-m-3 p-3 flex items-start rounded-lg text-base font-medium text-gray-900 hover:bg-bronze hover:text-gray-200"
                                                            >
                                                                {subMenuItem.name}
                                                            </Link>
                                                        ))}
                                                    </>
                                                );
                                            })()}
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    );
                }

                const classes = ['text-base text-white rounded-md'];

                if (menuItem.isEmphasized) {
                    classes.push('bg-crimson hover:bg-crimson-dark font-bold px-3');
                } else {
                    classes.push('bg-transparent hover:text-goldenrod font-normal');
                }

                return (
                    <Link
                        key={menuItem.link}
                        href={menuItem.link}
                        className={classes.join(' ')}
                    >
                        {menuItem.name}
                    </Link>
                );
            })}
        </>
    );
}
