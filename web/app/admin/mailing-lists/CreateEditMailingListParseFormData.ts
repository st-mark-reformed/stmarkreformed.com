import {
    CreateEditMailingListSubscriberValue,
    CreateEditMailingListValues,
} from './CreateEditMailingListValues';

function stringValue (value: FormDataEntryValue | undefined): string {
    return typeof value === 'string' ? value : '';
}

function connectionTypeValue (
    value: FormDataEntryValue | undefined,
): 'ssl' | 'tls' | 'none' {
    const stringified = stringValue(value);

    if (stringified === 'tls' || stringified === 'none') {
        return stringified;
    }

    return 'ssl';
}

export default function CreateEditMailingListParseFormData (
    formData: FormData,
): CreateEditMailingListValues {
    const names = formData.getAll('subscriberName');
    const emails = formData.getAll('subscriberEmail');

    const subscribers: CreateEditMailingListSubscriberValue[] = names
        .map((name, index) => ({
            name: stringValue(name).trim(),
            emailAddress: stringValue(emails[index]).trim(),
        }))
        .filter((subscriber) => subscriber.emailAddress !== '');

    return {
        listName: stringValue(formData.get('listName') ?? undefined),
        listAddress: stringValue(formData.get('listAddress') ?? undefined),
        imapServer: stringValue(formData.get('imapServer') ?? undefined),
        imapPort: stringValue(formData.get('imapPort') ?? undefined),
        connectionType: connectionTypeValue(
            formData.get('connectionType') ?? undefined,
        ),
        username: stringValue(formData.get('username') ?? undefined),
        password: stringValue(formData.get('password') ?? undefined),
        subscribers,
    };
}
