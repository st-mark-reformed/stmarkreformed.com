import { Series } from './series/Series';
import { Profile } from '../profiles/Profile';

export interface Message {
    id: string;
    title: string;
    slug: string;
    date: string;
    passage: string;
    series: Series;
    speaker: Profile;
    isEnabled: boolean;
    audioPath: string;
}
