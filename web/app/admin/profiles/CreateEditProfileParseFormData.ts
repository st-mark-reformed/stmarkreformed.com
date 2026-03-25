import { CreateEditProfileValues } from './CreateEditProfileValues';

export default function CreateEditProfileParseFormData (
    formData: FormData,
): CreateEditProfileValues {
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
        titleOrHonorific,
        email,
        firstName,
        lastName,
        leadershipPosition,
        bio,
    };
}
