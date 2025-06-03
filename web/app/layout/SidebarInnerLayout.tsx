import React, { ReactNode } from 'react';
import SidebarInnerLayoutNavSection from './SidebarInnerLayoutNavSection';
import { SidebarNavSection } from './SidebarNavSection';

export default function SidebarInnerLayout (
    {
        children,
        navSections = [],
        reducedPadding = false,
        topOfBodyContent = null,
        bottomOfBodyContent = null,
    }: {
        children: ReactNode;
        navSections?: Array<SidebarNavSection>;
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
                            <div>
                                {navSections.map((navSection, index) => (
                                    <SidebarInnerLayoutNavSection
                                        key={navSection.id}
                                        navSection={navSection}
                                        addTopMargin={index > 0}
                                    />
                                ))}
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
