import { ReactNode } from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle('Admin'),
};

/**
 * Reading env/secrets config requires loading dynamically at runtime rather
 * than build time. This ensures that all server components render dynamically
 * because this is our root layout.
 * @see https://nextjs.org/docs/app/api-reference/file-conventions/route-segment-config
 */
export const dynamic = 'force-dynamic';

export default async function Layout (
    { children }: { children: ReactNode },
) {
    return children;
}
