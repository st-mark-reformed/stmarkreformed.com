declare module 'smartypants' {
    export interface Options {
        dashes?: 'oldschool' | 'inverted' | 'default';
        quotes?: boolean;
        backticks?: boolean;
    }

    /**
     * Processes the given text with smart typography replacements.
     * @param text The text to process.
     * @param options Configuration options for processing.
     * @returns The processed text with replacements.
     */
    export function smartypants(text: string, options?: Options): string;

    export default smartypants;
}
