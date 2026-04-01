export type SubmitDeleteMessagesActionState = {
    status: 'success' | 'failure' | 'unsubmitted';
    iteration: number;
    message: string;
};
