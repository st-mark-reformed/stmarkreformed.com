'use client';

import React from 'react';
import { CheckIcon } from '@heroicons/react/20/solid';
import PageHeader from '../../../layout/PageHeader';

export default function PageInner () {
    return (
        <>
            <div className="mb-4 ">
                <PageHeader
                    title="Create New Message"
                    buttons={[
                        {
                            id: 'newEntry',
                            type: 'primary',
                            content: (
                                <>
                                    <CheckIcon className="h-5 w-5 mr-1" />
                                    Submit
                                </>
                            ),
                            onClick: () => {},
                        },
                    ]}
                />
            </div>
            <>TODO</>
        </>
    );
}
