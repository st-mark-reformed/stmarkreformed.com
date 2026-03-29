import { CreateEditMessageValues } from './CreateEditMessageValues';

export default function CreateEditMessageParseFormData (
    formData: FormData,
): CreateEditMessageValues {
    const titleValue = formData.get('title');
    const title = typeof titleValue === 'string' ? titleValue : '';

    const dateValue = formData.get('date');
    const date = typeof dateValue === 'string' ? dateValue : '';

    const passageValue = formData.get('passage');
    const passage = typeof passageValue === 'string' ? passageValue : '';

    const seriesIdValue = formData.get('seriesId');
    const seriesId = typeof seriesIdValue === 'string' ? seriesIdValue : '';

    const speakerIdValue = formData.get('speakerId');
    const speakerId = typeof speakerIdValue === 'string' ? speakerIdValue : '';

    const isEnabledValue = formData.get('isEnabled');
    const isEnabled = isEnabledValue === 'on';

    const audioPathValue = formData.get('audioPath');
    const audioPath = typeof audioPathValue === 'string' ? audioPathValue : '';

    return {
        title,
        date,
        passage,
        seriesId,
        speakerId,
        isEnabled,
        audioPath,
    };
}
