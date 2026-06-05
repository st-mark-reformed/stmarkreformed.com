export type CreateEditMailingListSubscriberValue = {
    name: string;
    emailAddress: string;
};

export type CreateEditMailingListValues = {
    listName: string;
    listAddress: string;
    imapServer: string;
    // Kept as a string for the form input; the API coerces it to an integer.
    imapPort: string;
    connectionType: 'ssl' | 'tls' | 'none';
    username: string;
    // Blank on edit means "keep the stored password" — it is never sent to the
    // browser.
    password: string;
    subscribers: CreateEditMailingListSubscriberValue[];
};
