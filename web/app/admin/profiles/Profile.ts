export interface Profile {
    id: string;
    titleOrHonorific: string;
    firstName: string;
    lastName: string;
    fullName: string;
    fullNameWithHonorific: string;
    email: string;
    leadershipPosition: string;
    leadershipPositionHumanReadable: string;
    bio: string;
    hasMessages: boolean;
}
