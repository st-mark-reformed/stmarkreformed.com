import { Profile } from '../../profiles/Profile';
import { MessageSeries } from './series-manager/MessageSeries';

export interface Message {
    id: string;
    isPublished: boolean;
    date: string | null;
    dateDisplay: string | null;
    title: string;
    slug: string;
    text: string;
    speaker: Profile | null;
    series: MessageSeries | null;
    audioFileName: string;
}
