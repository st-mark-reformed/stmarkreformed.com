import { CreateEditMenOfTheMarkValues } from './CreateEditMenOfTheMarkValues';

export default function CreateEditMenOfTheMarkParseFormData (
    formData: FormData,
): CreateEditMenOfTheMarkValues {
    const titleValue = formData.get('title');
    const title = typeof titleValue === 'string' ? titleValue : '';

    const slugValue = formData.get('slug');
    const slug = typeof slugValue === 'string' ? slugValue : '';

    const dateValue = formData.get('date');
    const date = typeof dateValue === 'string' ? dateValue : '';

    const bodyValue = formData.get('body');
    const body = typeof bodyValue === 'string' ? bodyValue : '';

    const isEnabledValue = formData.get('isEnabled');
    const isEnabled = isEnabledValue === 'on';

    return {
        title,
        slug,
        date,
        body,
        isEnabled,
    };
}
