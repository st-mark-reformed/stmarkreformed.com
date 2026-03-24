'use server';

type Values = {
    titleOrHonorific: string;
    email: string;
    firstName: string;
    lastName: string;
    leadershipPosition: string;
    bio: string;
};

export type SubmitFormActionState =
    | { ok: true; values: Values }
    | { ok: false; values: Values; errors: Record<string, string> };

export default async function SubmitFormAction (
    prevState: SubmitFormActionState,
    formData: FormData,
) {
    const titleOrHonorificValue = formData.get('titleOrHonorific');
    const titleOrHonorific = typeof titleOrHonorificValue === 'string'
        ? titleOrHonorificValue
        : '';

    const emailValue = formData.get('email');
    const email = typeof emailValue === 'string' ? emailValue : '';

    const firstNameValue = formData.get('firstName');
    const firstName = typeof firstNameValue === 'string' ? firstNameValue : '';

    const lastNameValue = formData.get('lastName');
    const lastName = typeof lastNameValue === 'string' ? lastNameValue : '';

    const leadershipPositionValue = formData.get('leadershipPosition');
    const leadershipPosition = typeof leadershipPositionValue === 'string'
        ? leadershipPositionValue
        : '';

    const bioValue = formData.get('bio');
    const bio = typeof bioValue === 'string' ? bioValue : '';

    return {
        ok: false,
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
        },
    };
}
