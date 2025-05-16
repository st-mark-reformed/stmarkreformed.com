import React, { ReactNode } from 'react';
import Link from 'next/link';

export interface SidebarLink {
    content: string | ReactNode;
    href: string;
    isActive: boolean;
}

export default function SidebarInnerLayout (
    {
        nav,
        children,
    }: {
        nav?: Array<SidebarLink>;
        children: ReactNode;
    },
) {
    return (
        <div className="min-h-screen-minus-header-and-footer">
            <div className="min-h-screen-minus-header-and-footer overflow-hidden md:flex">
                {/* Sidebar */}
                <div className="md:flex md:flex-shrink-0 bg-crimson">
                    <div className="mx-auto w-64 flex flex-col">
                        <div className="pt-5 pb-4 flex flex-col flex-grow overflow-y-auto">
                            <div className="flex-grow flex flex-col">
                                {(() => {
                                    if (!nav) {
                                        return null;
                                    }

                                    return (
                                        <nav className="flex-1 px-2 space-y-1">
                                            {nav.map((item, i) => {
                                                const classes = [
                                                    'text-white',
                                                    'group',
                                                    'rounded-md',
                                                    'py-2',
                                                    'px-2',
                                                    'flex',
                                                    'items-center',
                                                    'text-base',
                                                ];

                                                if (i === 0) {
                                                    classes.push('font-bold');
                                                } else {
                                                    classes.push('font-normal');
                                                }

                                                if (item.isActive) {
                                                    classes.push('bg-bronze');
                                                } else {
                                                    classes.push('hover:bg-bronze');
                                                }

                                                return (
                                                    <Link
                                                        key={item.href}
                                                        href={item.href}
                                                        className={classes.join(' ')}
                                                    >
                                                        {item.content}
                                                    </Link>
                                                );
                                            })}
                                        </nav>
                                    );
                                })()}
                            </div>
                        </div>
                    </div>
                </div>
                {/* Content */}
                <div className="flex-1 flex flex-col">
                    <div>
                        <div className="relative bg-white">
                            <div className="relative mx-auto px-4 py-12 sm:max-w-5xl sm:px-14 sm:py-20 md:py-28 lg:py-32 text-left">
                                <div className="mt-3 text-lg text-gray-600 prose max-w-none">
                                    {children}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
