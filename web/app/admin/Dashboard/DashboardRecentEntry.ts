// A single area entry, normalized so every area renders identically on the
// dashboard regardless of its underlying shape.
export interface DashboardRecentEntry {
    id: string;
    title: string;
    meta: string;
    editHref: string;
}
