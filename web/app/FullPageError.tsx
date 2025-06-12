import React from 'react';
import Link from 'next/link';

export interface ErrorParams {
    statusCode?: number;
    heading?: string;
    errorMessage?: string;
}

export default function FullPageError (
    {
        statusCode = 500,
        heading = 'An Error Occurred',
        errorMessage = 'It looks like something went wrong 😞',
    }: ErrorParams,
) {
    return (
        <div className="bg-white min-h-full px-4 py-16 sm:px-6 sm:py-24 md:py-32 lg:py-48 md:grid md:place-items-center lg:px-8">
            <div className="max-w-max mx-auto">
                <main className="sm:flex">
                    <p className="text-4xl font-extrabold text-crimson sm:text-5xl">
                        {statusCode}
                    </p>
                    <div className="sm:ml-6">
                        <div className="sm:border-l sm:border-gray-200 sm:pl-6">
                            <h1 className="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                                {heading}
                            </h1>
                            <p className="mt-1 text-base text-gray-500">
                                {errorMessage}
                            </p>
                        </div>
                        <div className="mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6">
                            <Link
                                href="/"
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-crimson hover:bg-crimson-dark focus:outline-none"
                            >
                                Go back home
                            </Link>
                            <Link
                                href="/contact"
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-crimson bg-lightest-red hover:bg-lighter-red focus:outline-none"
                            >
                                Contact us
                            </Link>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    );
}
