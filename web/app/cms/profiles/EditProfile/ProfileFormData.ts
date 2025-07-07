export const LeadershipPosition = {
    PASTOR: 'Pastor',
    ASSOCIATE_PASTOR: 'Associate Pastor',
    ASSISTANT_PASTOR: 'Assistant Pastor',
    ELDER: 'Elder',
    RULING_ELDER: 'Ruling Elder',
    DEACON: 'Deacon',
};

export type LeadershipPositionType = typeof LeadershipPosition[keyof typeof LeadershipPosition] | '';

export interface ProfileFormData {
    slug: string;
    firstName: string;
    lastName: string;
    titleOrHonorific: string;
    email: string;
    leadershipPosition: LeadershipPositionType;
}

export function getLeadershipPositionKeyByValue (value: string): string {
    return Object.entries(LeadershipPosition).find(([, v]) => v === value)?.[0] || '';
}
