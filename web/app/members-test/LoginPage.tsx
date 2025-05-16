import React from 'react';
import { Metadata } from 'next';
import Layout from '../layout/Layout';
import { createPageTitle } from '../createPageTitle';
import LoginPageClientSide from './LoginPageClientSide';

export const metadata: Metadata = {
    title: createPageTitle('Members'),
};

export default async function LoginPage () {
    return (
        <Layout hero={{ heroHeading: 'Log in to view the members area' }}>
            <div className="min-h-screen-minus-header-and-footer bg-gray-50">
                <div className="min-h-screen-minus-header-and-footer overflow-hidden md:flex">
                    <div className="flex-1 flex flex-col">
                        <div className="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
                            <div className="my-8 sm:mx-auto sm:w-full sm:max-w-md">
                                <div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                                    <LoginPageClientSide />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
