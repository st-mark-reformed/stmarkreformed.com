import { CreateEditSeriesValues } from './CreateEditSeriesValues';

export default function CreateEditSeriesParseFormData (
    formData: FormData,
): CreateEditSeriesValues {
    const titleValue = formData.get('title');
    const title = typeof titleValue === 'string' ? titleValue : '';

    const slugValue = formData.get('slug');
    const slug = typeof slugValue === 'string' ? slugValue : '';

    return {
        title,
        slug,
    };
}
