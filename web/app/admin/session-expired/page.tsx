import React from 'react';
import Image from 'next/image';
import { Metadata } from 'next';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Session Expired',
        'Admin',
    ]),
};

export default async function Page (
    {
        searchParams,
    }: {
        searchParams: Promise<{ returnTo?: string }>;
    },
) {
    const { returnTo } = await searchParams;

    const signInHref = returnTo
        ? `/api/auth/sign-in?authReturn=${encodeURIComponent(returnTo)}`
        : '/api/auth/sign-in';

    return (
        <div className="bg-white min-h-full px-4 py-16 sm:px-6 sm:py-24 md:py-32 lg:py-48 md:grid md:place-items-center lg:px-8">
            <div className="max-w-max mx-auto text-center">
                <Image
                    alt="St. Mark Reformed Church"
                    src="/images/logo/logo-website-header.png"
                    width={88}
                    height={80}
                    className="mx-auto mb-8"
                />
                <h1 className="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                    Session Expired
                </h1>
                <p className="mt-3 text-base text-gray-500">
                    Your session has expired. Please log back in to continue.
                </p>
                <div className="mt-10">
                    <a
                        href={signInHref}
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-crimson hover:bg-crimson-dark focus:outline-none"
                    >
                        Log back in
                    </a>
                </div>
            </div>
        </div>
    );
}
