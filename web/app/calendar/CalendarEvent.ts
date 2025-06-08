export interface CalendarEvent {
    uid: string;
    summary: string;
    description: string;
    location: string;
    isInPast: boolean;
    startDate: string;
    endDate: string;
    isMultiDay: boolean;
    isAllDay: boolean;
    totalDays: number;
}
