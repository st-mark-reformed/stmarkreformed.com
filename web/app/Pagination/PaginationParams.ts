export type PaginationParams = {
    baseUrl?: string;
    currentPage?: number;
    totalPages?: number;
    pad?: number;
    className?: string | undefined;
    queryString?: string;
    // The public site is light-only, so dark-mode styles are opt-in
    darkModeAware?: boolean;
};
