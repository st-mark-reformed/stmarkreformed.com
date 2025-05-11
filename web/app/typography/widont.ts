import { stripHtml } from 'string-strip-html';
import { removeWidows } from 'string-remove-widows';

export default function widont (htmlOrText: string): string {
    const stripped = stripHtml(htmlOrText);

    if (!stripped.ranges) {
        return removeWidows(htmlOrText).res;
    }

    const paragraphs = htmlOrText.split('</p>').filter((text) => text.trim() !== '');

    return paragraphs.map((paragraph) => {
        paragraph += '</p>';

        const strippedHtml = stripHtml(paragraph);

        if (!strippedHtml.ranges) {
            return removeWidows(paragraph).res;
        }

        return removeWidows(paragraph, {
            tagRanges: strippedHtml.ranges.map(([from, to]) => [from, to]),
        }).res;
    }).join('');
}
