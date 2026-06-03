export type SubmitDeleteNewsActionState = {
    status: 'success' | 'failure' | 'unsubmitted';
    iteration: number;
    message: string;
};
