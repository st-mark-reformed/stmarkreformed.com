export type UploadAcceptType = 'mp3' | 'pdf' | 'any';

const CHUNK_SIZE = 2 * 1024 * 1024;
const MAX_CHUNK_RETRIES = 5;
const RETRY_BASE_DELAY_MS = 500;
const UPLOADS_BASE = '/api/admin/uploads';

interface UploadProgressResponse {
    uploadId: string;
    receivedChunks: Array<number>;
    totalChunks: number;
}

interface UploadCompleteResponse {
    success: boolean;
    errors: Record<string, string>;
}

export interface ChunkedUploadOptions {
    acceptType: UploadAcceptType;

    // Namespaces the resume entry so two fields/rows never collide.
    resumeKey: string;

    onProgress?: (fraction: number) => void;
    signal?: AbortSignal;
}

function storageKey (resumeKey: string): string {
    return `chunked-upload:${resumeKey}`;
}

function readStoredUploadId (resumeKey: string): string | null {
    try {
        return window.sessionStorage.getItem(storageKey(resumeKey));
    } catch {
        return null;
    }
}

function writeStoredUploadId (resumeKey: string, uploadId: string): void {
    try {
        window.sessionStorage.setItem(storageKey(resumeKey), uploadId);
    } catch {
        // Resumability is best-effort; ignore storage failures.
    }
}

function clearStoredUploadId (resumeKey: string): void {
    try {
        window.sessionStorage.removeItem(storageKey(resumeKey));
    } catch {
        // Ignore storage failures.
    }
}

function wait (ms: number, signal?: AbortSignal): Promise<void> {
    return new Promise((resolve, reject) => {
        if (signal?.aborted) {
            reject(new DOMException('Aborted', 'AbortError'));

            return;
        }

        const timeout = setTimeout(resolve, ms);

        signal?.addEventListener(
            'abort',
            () => {
                clearTimeout(timeout);
                reject(new DOMException('Aborted', 'AbortError'));
            },
            { once: true },
        );
    });
}

async function fetchStatus (
    uploadId: string,
    signal?: AbortSignal,
): Promise<UploadProgressResponse | null> {
    const response = await fetch(
        `${UPLOADS_BASE}/${encodeURIComponent(uploadId)}`,
        { method: 'GET', signal },
    );

    if (!response.ok) {
        return null;
    }

    return await response.json() as UploadProgressResponse;
}

async function initiateUpload (
    file: File,
    totalChunks: number,
    acceptType: UploadAcceptType,
    signal?: AbortSignal,
): Promise<UploadProgressResponse> {
    const response = await fetch(UPLOADS_BASE, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            fileName: file.name,
            totalSize: file.size,
            chunkSize: CHUNK_SIZE,
            totalChunks,
            acceptType,
        }),
        signal,
    });

    if (!response.ok) {
        throw new Error('Could not start the upload. Please try again.');
    }

    return await response.json() as UploadProgressResponse;
}

async function putChunk (
    uploadId: string,
    index: number,
    blob: Blob,
    signal?: AbortSignal,
): Promise<void> {
    let attempt = 0;

    for (;;) {
        try {
            const response = await fetch(
                `${UPLOADS_BASE}/${encodeURIComponent(uploadId)}/chunk/${index}`,
                {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/octet-stream' },
                    body: blob,
                    signal,
                },
            );

            if (response.ok) {
                return;
            }
        } catch (error) {
            if (signal?.aborted) {
                throw error;
            }

            // Otherwise fall through and retry with backoff.
        }

        attempt += 1;

        if (attempt >= MAX_CHUNK_RETRIES) {
            throw new Error(
                'A piece of the upload failed repeatedly. Check your connection and try again.',
            );
        }

        await wait(RETRY_BASE_DELAY_MS * (2 ** (attempt - 1)), signal);
    }
}

async function completeUpload (
    uploadId: string,
    signal?: AbortSignal,
): Promise<UploadCompleteResponse> {
    const response = await fetch(
        `${UPLOADS_BASE}/${encodeURIComponent(uploadId)}/complete`,
        { method: 'POST', signal },
    );

    return await response.json() as UploadCompleteResponse;
}

async function resolveUploadId (
    file: File,
    totalChunks: number,
    options: ChunkedUploadOptions,
): Promise<string> {
    const stored = readStoredUploadId(options.resumeKey);

    if (stored) {
        const status = await fetchStatus(stored, options.signal);

        if (status && status.totalChunks === totalChunks) {
            return stored;
        }
    }

    const initiated = await initiateUpload(
        file,
        totalChunks,
        options.acceptType,
        options.signal,
    );

    writeStoredUploadId(options.resumeKey, initiated.uploadId);

    return initiated.uploadId;
}

/**
 * Uploads a file in small chunks with per-chunk retry and resume-after-drop, so
 * a flaky connection only ever loses the in-flight chunk. Returns the upload
 * handle (uploadId) to attach to the owning form's hidden input.
 */
export async function uploadFileResumably (
    file: File,
    options: ChunkedUploadOptions,
): Promise<string> {
    const totalChunks = Math.max(1, Math.ceil(file.size / CHUNK_SIZE));

    const uploadId = await resolveUploadId(file, totalChunks, options);

    const status = await fetchStatus(uploadId, options.signal);
    const received = new Set<number>(status?.receivedChunks ?? []);

    for (let index = 0; index < totalChunks; index++) {
        if (!received.has(index)) {
            const start = index * CHUNK_SIZE;
            const blob = file.slice(start, Math.min(start + CHUNK_SIZE, file.size));

            await putChunk(uploadId, index, blob, options.signal);
        }

        options.onProgress?.((index + 1) / totalChunks);
    }

    const result = await completeUpload(uploadId, options.signal);

    clearStoredUploadId(options.resumeKey);

    if (!result.success) {
        const firstError = Object.values(result.errors ?? {})[0];

        throw new Error(firstError ?? 'The upload could not be completed.');
    }

    return uploadId;
}
