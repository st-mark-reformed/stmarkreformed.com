export type SubmitDeleteHymnsOfTheMonthActionState = {
    status: 'success' | 'failure' | 'unsubmitted';
    iteration: number;
    message: string;
};
