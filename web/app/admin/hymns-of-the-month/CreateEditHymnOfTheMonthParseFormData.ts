import {
    CreateEditHymnOfTheMonthPracticeTrackValue,
    CreateEditHymnOfTheMonthValues,
} from './CreateEditHymnOfTheMonthValues';

function stringValue (value: FormDataEntryValue | undefined): string {
    return typeof value === 'string' ? value : '';
}

export default function CreateEditHymnOfTheMonthParseFormData (
    formData: FormData,
): CreateEditHymnOfTheMonthValues {
    const month = stringValue(formData.get('month') ?? undefined);
    const hymnPsalmName = stringValue(formData.get('hymnPsalmName') ?? undefined);
    const musicSheet = stringValue(formData.get('musicSheet') ?? undefined);
    const isEnabled = formData.get('isEnabled') === 'on';

    const titles = formData.getAll('practiceTrackTitle');
    const files = formData.getAll('practiceTrackFile');

    const practiceTracks: CreateEditHymnOfTheMonthPracticeTrackValue[] = titles
        .map((title, index) => ({
            title: stringValue(title),
            file: stringValue(files[index]),
        }))
        .filter((track) => track.title !== '' || track.file !== '');

    return {
        isEnabled,
        month,
        hymnPsalmName,
        musicSheet,
        practiceTracks,
    };
}
