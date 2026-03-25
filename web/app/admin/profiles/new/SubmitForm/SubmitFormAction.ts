'use server';

import { Values } from './Values';
import ParseFormData from './ParseFormData';

export type SubmitFormActionState =
    | {
        ok: true;
        success: boolean;
        values: Values;
    }
    | {
        ok: false;
        success: boolean;
        values: Values;
        errors: Record<string, string>;
    };

export default async function SubmitFormAction (
    prevState: SubmitFormActionState,
    formData: FormData,
) {
    const {
        titleOrHonorific,
        email,
        firstName,
        lastName,
        leadershipPosition,
        bio,
    } = ParseFormData(formData);

    return {
        ok: false,
        success: false,
        values: {
            titleOrHonorific,
            email,
            firstName,
            lastName,
            leadershipPosition,
            bio,
        },
        errors: {
            email: 'Email is required',
            firstName: 'First name is required',
            lastName: 'Last name is required',
        },
    };
}
