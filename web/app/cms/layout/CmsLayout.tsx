import React, { ReactNode, Suspense } from 'react';
import { RequestResponse } from 'rxante-oauth/src/Request/RequestResponse';
import Sidebar from './Sidebar/Sidebar';
import Breadcrumbs, { BreadcrumbItems, CurrentBreadcrumbItem } from '../../Breadcrumbs';
import PartialPageLoading from '../../PartialPageLoading';
import FullPageError from '../../FullPageError';
import { TokenRepositoryFactory } from '../../api/auth/TokenRepositoryFactory';
import { RequestFactory } from '../../api/request/RequestFactory';

export enum InnerMaxWidth {
    xsmall = 'max-w-3xl',
    small = 'max-w-4xl',
    medium = 'max-w-5xl',
    large = 'max-w-6xl',
    xlarge = 'max-w-7-xl',
}

export default async function CmsLayout (
    {
        children,
        breadcrumbs,
        innerMaxWidth = InnerMaxWidth.medium,
    }: {
        children: ReactNode;
        breadcrumbs?: {
            breadcrumbs: BreadcrumbItems;
            currentBreadcrumb: CurrentBreadcrumbItem;
        };
        innerMaxWidth?: InnerMaxWidth;
    },
) {
    const token = await TokenRepositoryFactory().findTokenFromCookies();

    if (token === null) {
        // This will see that we don't have a token and trigger a redirect
        await RequestFactory().makeWithSignInRedirect({
            uri: '/has-cms-access',
            cacheSeconds: 0,
        });
    }

    return (
        <div>
            <Sidebar />
            <div className="lg:pl-72">
                {(() => {
                    if (!breadcrumbs) {
                        return null;
                    }

                    return (
                        <Breadcrumbs
                            breadcrumbs={breadcrumbs.breadcrumbs}
                            currentBreadcrumb={breadcrumbs.currentBreadcrumb}
                        />
                    );
                })()}
                <main className="">
                    <div className="p-4 sm:p-6 md:p-8">
                        <Suspense fallback={<PartialPageLoading />}>
                            <div className={innerMaxWidth}>
                                {children}
                            </div>
                        </Suspense>
                    </div>
                </main>
            </div>
        </div>
    );
}
