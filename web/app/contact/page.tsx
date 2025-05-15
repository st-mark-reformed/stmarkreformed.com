import React from 'react';
import { Metadata } from 'next';
import Layout from '../layout/Layout';
import { createPageTitle } from '../createPageTitle';
import ContactFormClientSide from './ContactFormClientSide';
import { MailingAddressDisplay } from './Address';

export const metadata: Metadata = {
    title: createPageTitle('Get in touch'),
};

export default function ContactPage () {
    return (
        <Layout hero={{ heroHeading: 'Get in touch' }}>
            <div className="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
                <div className="relative bg-gray-100 border border-gray-200 shadow-xl">
                    <div className="grid grid-cols-1 lg:grid-cols-3">
                        <div className="relative overflow-hidden py-10 px-6 bg-crimson sm:px-10 xl:p-12">
                            <div className="prose text-gray-100 prose-over-dark">
                                <p>Need to get in touch with us? Need information? Want to find out more? Fill out the form and Pastor Thacker or one of the officers will get in touch with you.</p>
                                <p>For written correspondence, please send mail to:</p>
                                <p><MailingAddressDisplay /></p>
                            </div>
                        </div>
                        <div className="py-10 px-6 sm:px-10 lg:col-span-2 xl:p-12">
                            <ContactFormClientSide />
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
