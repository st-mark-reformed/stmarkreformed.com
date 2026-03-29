import { CreateEditMessageValues } from './CreateEditMessageValues';

export type CreateEditMessageSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditMessageValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditMessageValues;
        errors: Record<string, string>;
    };
