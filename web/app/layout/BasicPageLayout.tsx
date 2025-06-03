import React, { ReactNode } from 'react';
import { HeroType } from './HeroType';
import Layout from './Layout';

export default function BasicPageLayout (
    {
        children,
        hero = null,
    }: {
        children: ReactNode;
        hero?: null | HeroType;
    },
) {
    return (
        <Layout hero={hero}>
            <div className="relative mx-auto px-4 py-12 sm:max-w-5xl sm:px-14 sm:py-20 md:py-28 lg:py-32 text-left">
                <div className="mt-3 text-lg text-gray-600 prose max-w-none">
                    {children}
                </div>
            </div>
        </Layout>
    );
}
