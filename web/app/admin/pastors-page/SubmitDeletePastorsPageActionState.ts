export type SubmitDeletePastorsPageActionState = {
    status: 'success' | 'failure' | 'unsubmitted';
    iteration: number;
    message: string;
};
