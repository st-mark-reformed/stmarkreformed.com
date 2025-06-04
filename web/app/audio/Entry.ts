export interface Entry {
    uid: string;
    title: string;
    slug: string;
    postDate: string;
    postDateDisplay: string;
    by: null | {
        title: string;
        slug: string;
    };
    text: string;
    series: null | {
        title: string;
        slug: string;
    };
    audioFileName: string | null;
}
