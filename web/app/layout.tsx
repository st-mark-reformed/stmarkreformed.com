import React, { ReactNode } from 'react';
import { Metadata } from 'next';
import { createPageTitle } from './createPageTitle';

import './style.css';

export const metadata: Metadata = {
    title: createPageTitle(''),
    icons: {
        icon: '/favicon.ico',
        shortcut: '/favicon.ico',
    },
};

export default async function RootLayout (
    { children }: { children: ReactNode },
) {
    return (
        <html lang="en" className="h-full">
            <body className="h-full">
                {children}
            </body>
        </html>
    );
}
