import { CreateEditPastorsPageValues } from './CreateEditPastorsPageValues';

export default function CreateEditPastorsPageParseFormData (
    formData: FormData,
): CreateEditPastorsPageValues {
    const titleValue = formData.get('title');
    const title = typeof titleValue === 'string' ? titleValue : '';

    const slugValue = formData.get('slug');
    const slug = typeof slugValue === 'string' ? slugValue : '';

    const dateValue = formData.get('date');
    const date = typeof dateValue === 'string' ? dateValue : '';

    const headingValue = formData.get('heading');
    const heading = typeof headingValue === 'string' ? headingValue : '';

    const subheadingValue = formData.get('subheading');
    const subheading = typeof subheadingValue === 'string' ? subheadingValue : '';

    const bodyValue = formData.get('body');
    const body = typeof bodyValue === 'string' ? bodyValue : '';

    const isEnabledValue = formData.get('isEnabled');
    const isEnabled = isEnabledValue === 'on';

    return {
        title,
        slug,
        date,
        heading,
        subheading,
        body,
        isEnabled,
    };
}
