'use server';

import { FormValues } from './FormValues';
import { ConfigOptions, getConfigString } from '../ServerSideRunTimeConfig';

export default async function PostContactForm (
    formValues: FormValues,
): Promise<{
    success: boolean;
    message: string;
    errors?: Array<string>;
}> {
    const headers = new Headers({
        RequestType: 'api',
        Accept: 'application/json',
        'Content-Type': 'application/json',
    });

    const body = JSON.stringify(formValues);

    const options = {
        redirect: 'manual',
        method: 'POST',
        headers,
        body,
    } as RequestInit;

    const response = await fetch(
        `${getConfigString(ConfigOptions.API_URL)}/contact`,
        options,
    );

    try {
        const json = await response.json();

        if (!response.ok) {
            return {
                success: false,
                message: json.message || 'An unknown error occurred',
                errors: json.errors || [],
            };
        }

        return {
            success: response.ok && json.success,
            message: json.message || 'An unknown error occurred',
            errors: json.errors || [],
        };
    } catch (error) {
        return {
            success: false,
            message: 'An unknown error occurred',
        };
    }
}
