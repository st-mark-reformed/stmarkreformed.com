export type SubmitDeleteMenOfTheMarkActionState = {
    status: 'success' | 'failure' | 'unsubmitted';
    iteration: number;
    message: string;
};
