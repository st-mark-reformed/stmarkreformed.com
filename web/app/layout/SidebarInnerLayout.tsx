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
        navHeading = null,
        reducedPadding = false,
        topOfBodyContent = null,
        bottomOfBodyContent = null,
    }: {
        nav?: Array<SidebarLink>;
        children: ReactNode;
        navHeading?: string | null;
        reducedPadding?: boolean;
        topOfBodyContent?: ReactNode | string | null;
        bottomOfBodyContent?: ReactNode | string | null;
    },
) {
    const contentWrapperClasses = [
        'relative',
        'mx-auto',
        'sm:max-w-5xl',
        'text-left',
        'px-4',
    ];

    if (reducedPadding) {
        contentWrapperClasses.push(
            'py-4',
            'sm:px-8',
            'sm:py-6',
            'md:py-8',
        );
    } else {
        contentWrapperClasses.push(
            'py-12',
            'sm:px-14',
            'sm:py-20',
            'md:py-28',
            'lg:py-32',
        );
    }

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
                                            {(() => {
                                                if (!navHeading) {
                                                    return null;
                                                }

                                                return (
                                                    <span
                                                        className="text-white group rounded-md py-2 px-2 flex items-center text-lg font-bold uppercase"
                                                    >
                                                        {navHeading}
                                                    </span>
                                                );
                                            })()}
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

                                                if (i === 0 && navHeading === null) {
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
                        {topOfBodyContent}
                        <div className="relative bg-white">
                            <div className={contentWrapperClasses.join(' ')}>
                                <div className="mt-3 text-lg text-gray-600 prose max-w-none">
                                    {children}
                                </div>
                            </div>
                        </div>
                        {bottomOfBodyContent}
                    </div>
                </div>
            </div>
        </div>
    );
}
