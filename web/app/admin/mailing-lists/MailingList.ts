export interface MailingListSubscriber {
    id: string;
    name: string;
    emailAddress: string;
}

export interface MailingList {
    id: string;
    listName: string;
    listAddress: string;
    imapServer: string;
    imapPort: number;
    connectionType: 'ssl' | 'tls' | 'none';
    username: string;
    // The IMAP password is never sent to the browser.
    subscribers: MailingListSubscriber[];
}
