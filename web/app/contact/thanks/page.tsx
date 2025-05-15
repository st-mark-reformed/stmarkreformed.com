import React from 'react';
import { Metadata } from 'next';
import Link from 'next/link';
import { createPageTitle } from '../../createPageTitle';
import Layout from '../../layout/Layout';

const title = 'Thank you for contacting us!';

export const metadata: Metadata = {
    title: createPageTitle(title),
};

export default function ContactThanksPage () {
    return (
        <Layout hero={{ heroHeading: title }}>
            <div className="relative mx-auto px-4 py-12 sm:max-w-5xl sm:px-14 sm:py-20 md:py-28 lg:py-32 text-center">
                <div className="mt-3 text-lg text-gray-600 prose max-w-none">
                    <p>Thank you for contacting us! Your message is on its way to our inboxes and we’ll be in touch with you as soon as we&nbsp;can.</p>
                    <p>Have a blessed&nbsp;day!</p>
                </div>
                <div className="mt-8 text-center">
                    <div>
                        <div className="inline-flex rounded-md shadow">
                            <Link
                                href="/"
                                className="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-gray-100 bg-crimson hover:bg-crimson-dark"
                            >
                                Back to Home Page
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
