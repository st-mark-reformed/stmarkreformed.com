import React from 'react';
import Link from 'next/link';
import { SidebarNavSection } from './SidebarNavSection';

export default function SidebarInnerLayoutNavSection (
    {
        navSection,
        addTopMargin = false,
    }: {
        navSection: SidebarNavSection;
        addTopMargin?: boolean;
    },
) {
    const { heading, nav } = navSection;

    return (
        <nav className={(() => {
            const classes = [
                'flex-1',
                'px-2',
                'space-y-1',
            ];

            if (addTopMargin) {
                classes.push('mt-10');
            }

            return classes.join(' ');
        })()}
        >
            {(() => {
                if (!heading) {
                    return null;
                }

                return (
                    <span className="text-white group rounded-md py-2 px-2 flex items-center text-lg font-bold uppercase">
                        {heading}
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

                if (i === 0 && !heading) {
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
}
