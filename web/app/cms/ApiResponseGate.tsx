import { RequestResponse } from 'rxante-oauth/src/Request/RequestResponse';
import React, { ReactNode } from 'react';
import { ErrorParams } from '../FullPageError';

export default async function ApiResponseGate (
    {
        children,
        apiResponse,
    }: {
        children: ReactNode;
        apiResponse: RequestResponse;
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

    return <>{children}</>;
}
