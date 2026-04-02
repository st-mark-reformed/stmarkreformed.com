export type SubmitDeleteSeriesActionState = {
    status: 'success' | 'failure' | 'unsubmitted';
    iteration: number;
    message: string;
};
