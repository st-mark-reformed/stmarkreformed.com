import React, { ReactNode } from 'react';
import Layout from '../layout/Layout';
import LoginGuard from './LoginGuard';
import SidebarInnerLayout from '../layout/SidebarInnerLayout';

export default function MemberLayout (
    {
        children,
        heroHeading,
        activeNavHref,
        topOfBodyContent = null,
        bottomOfBodyContent = null,
    }: {
        children: ReactNode;
        heroHeading: string;
        activeNavHref: string;
        topOfBodyContent?: ReactNode | string | null;
        bottomOfBodyContent?: ReactNode | string | null;
    },
) {
    return (
        <LoginGuard>
            <Layout hero={{ heroHeading }}>
                <SidebarInnerLayout
                    reducedPadding
                    navHeading="Members Area"
                    topOfBodyContent={topOfBodyContent}
                    bottomOfBodyContent={bottomOfBodyContent}
                    nav={[
                        {
                            content: 'Internal Media',
                            href: '/members/internal-media',
                            isActive: activeNavHref === '/members/internal-media',
                        },
                        {
                            content: 'Hymns of the Month',
                            href: '/members/hymns-of-the-month',
                            isActive: activeNavHref === '/members/hymns-of-the-month',
                        },
                    ]}
                >
                    {children}
                </SidebarInnerLayout>
            </Layout>
        </LoginGuard>
    );
}
