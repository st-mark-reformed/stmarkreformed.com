// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/no-unused-vars */
// noinspection JSUnusedLocalSymbols

import React, { ReactNode } from 'react';
import { Metadata } from 'next';
import { Open_Sans } from 'next/font/google';
import { createPageTitle } from './createPageTitle';

import './style.css';

export const metadata: Metadata = {
    title: createPageTitle(''),
    icons: {
        icon: '/favicon.ico',
        shortcut: '/favicon.ico',
    },
    assets: [],
    openGraph: {
        images: [
            {
                url: 'https://www.stmarkreformed.com/share.png',
            },
        ],
    },
};

const openSansI300Init = Open_Sans({
    style: 'italic',
    weight: '300',
    variable: '--font-open-sans',
    subsets: ['latin', 'latin-ext', 'math'],
    display: 'swap',
});

const openSansI400Init = Open_Sans({
    style: 'italic',
    weight: '400',
    variable: '--font-open-sans',
    subsets: ['latin', 'latin-ext', 'math'],
    display: 'swap',
});

const openSansI800Init = Open_Sans({
    style: 'italic',
    weight: '800',
    variable: '--font-open-sans',
    subsets: ['latin', 'latin-ext', 'math'],
    display: 'swap',
});

const openSansN300Init = Open_Sans({
    style: 'normal',
    weight: '300',
    variable: '--font-open-sans',
    subsets: ['latin', 'latin-ext', 'math'],
    display: 'swap',
});

const openSansN400Init = Open_Sans({
    style: 'normal',
    weight: '400',
    variable: '--font-open-sans',
    subsets: ['latin', 'latin-ext', 'math'],
    display: 'swap',
});

const openSansN800Init = Open_Sans({
    style: 'normal',
    weight: '800',
    variable: '--font-open-sans',
    subsets: ['latin', 'latin-ext', 'math'],
    display: 'swap',
});

export default async function RootLayout (
    { children }: { children: ReactNode },
) {
    return (
        <html
            lang="en"
            className="h-full"
        >
            <body className="h-full">
                {children}
            </body>
        </html>
    );
}
