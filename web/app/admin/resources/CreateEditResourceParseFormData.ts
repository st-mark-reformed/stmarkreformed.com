import {
    CreateEditResourceDownloadValue,
    CreateEditResourceValues,
} from './CreateEditResourceValues';

function stringValue (value: FormDataEntryValue | undefined): string {
    return typeof value === 'string' ? value : '';
}

export default function CreateEditResourceParseFormData (
    formData: FormData,
): CreateEditResourceValues {
    const title = stringValue(formData.get('title') ?? undefined);
    const slug = stringValue(formData.get('slug') ?? undefined);
    const date = stringValue(formData.get('date') ?? undefined);
    const body = stringValue(formData.get('body') ?? undefined);
    const isEnabled = formData.get('isEnabled') === 'on';

    const filenames = formData.getAll('downloadFilename');
    const files = formData.getAll('downloadFile');

    const downloads: CreateEditResourceDownloadValue[] = filenames
        .map((filename, index) => ({
            filename: stringValue(filename),
            file: stringValue(files[index]),
        }))
        .filter((download) => download.filename !== '' || download.file !== '');

    return {
        isEnabled,
        date,
        title,
        slug,
        body,
        downloads,
    };
}
