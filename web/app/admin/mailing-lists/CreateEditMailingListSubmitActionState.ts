import { CreateEditMailingListValues } from './CreateEditMailingListValues';

export type CreateEditMailingListSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditMailingListValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditMailingListValues;
        errors: Record<string, string>;
    };
