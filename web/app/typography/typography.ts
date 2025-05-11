import smartypants from 'smartypants';
import widont from './widont';

export default function typography (htmlOrText: string): string {
    return smartypants(widont(htmlOrText));
}
