import { RequestResponse } from 'rxante-oauth/src/Request/RequestResponse';
import React, { ReactNode } from 'react';
import { notFound } from 'next/navigation';
import { ErrorParams } from '../FullPageError';

export default async function ApiResponseGate (
    {
        children,
        apiResponse,
    }: {
        children: ReactNode;
        apiResponse?: RequestResponse;
    },
) {
    if (apiResponse !== undefined && apiResponse.status === 403) {
        const accessDefined: ErrorParams = {
            statusCode: 403,
            heading: 'Access Denied',
            // @ts-expect-error TS2339
            errorMessage: apiResponse.json?.message,
        };

        throw new Error(`access_denied|${JSON.stringify(accessDefined)}`);
    }

    if (apiResponse !== undefined && apiResponse.status === 404) {
        notFound();
    }

    if (apiResponse !== undefined && apiResponse.status !== 200) {
        throw new Error(`API Error: ${JSON.stringify(apiResponse.json)}`);
    }

    return <>{children}</>;
}
