export type CreateEditResourceDownloadValue = {
    // The stored/original filename, shown to the editor and used in the URL.
    filename: string;
    // Empty for an already-stored file, or a base64 data URI for a new upload.
    file: string;
};

export type CreateEditResourceValues = {
    isEnabled: boolean;
    date: string;
    title: string;
    slug: string;
    body: string;
    downloads: CreateEditResourceDownloadValue[];
};
