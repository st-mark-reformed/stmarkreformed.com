export interface MessageFormData {
    published: boolean;
    date: Date | null;
    title: string;
    slug: string;
    text: string;
    speakerId: string;
    seriesId: string;
    audioFile: string;
}
