export interface MessageFormData {
    published: boolean;
    date: Date | null;
    title: string;
    text: string;
    speakerId: string;
    audioFile: string;
}
