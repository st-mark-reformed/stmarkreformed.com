export type SubmitDeleteProfilesActionState = {
    status: 'success' | 'failure' | 'unsubmitted';
    iteration: number;
    message: string;
};
