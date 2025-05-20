export default function GetMimeType (filePath: string): string {
    const extension = filePath.split('.').pop()?.toLowerCase();

    const mimeTypes: Record<string, string> = {
        pdf: 'application/pdf',
        mp3: 'audio/mpeg',
        wav: 'audio/wav',
        jpg: 'image/jpeg',
        jpeg: 'image/jpeg',
        png: 'image/png',
        txt: 'text/plain',
        html: 'text/html',
        css: 'text/css',
        js: 'text/javascript',
        json: 'application/json',
        xml: 'application/xml',
    };

    return mimeTypes[extension ?? ''] || 'application/octet-stream';
}
