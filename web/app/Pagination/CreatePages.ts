import { PaginationParams } from './PaginationParams';

export type PaginationItem = {
    label: string;
    href: string;
    isActive: boolean;
};

export default function CreatePages (
    {
        baseUrl = '',
        currentPage = 1,
        totalPages = 1,
        pad = 2,
    }: PaginationParams,
) {
    if (totalPages < 2) {
        return [];
    }

    let lowerRange = currentPage - pad;
    let upperRange = currentPage + pad;

    // Figure out if we're starting from one or ending at total
    if (currentPage < pad + 1) {
        lowerRange = 1;
        upperRange = (pad * 2) + 1;
    } else if (currentPage + pad >= totalPages) {
        lowerRange = totalPages - (pad * 2);
        upperRange = totalPages;
    }

    // Sanity check lower range
    lowerRange = lowerRange < 1 ? 1 : lowerRange;

    // Sanity check upper range
    if (upperRange > totalPages) {
        upperRange = totalPages;
    }

    const pages: Array<PaginationItem> = [];

    for (let pageNum = lowerRange; pageNum <= upperRange; pageNum += 1) {
        pages.push({
            label: pageNum.toString(),
            href: pageNum === 1 ? baseUrl : `${baseUrl}/page/${pageNum}`,
            isActive: pageNum === currentPage,
        });
    }

    return pages;
}
