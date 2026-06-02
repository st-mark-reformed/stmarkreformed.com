import { CreateEditInternalMessageValues } from './CreateEditInternalMessageValues';

export type CreateEditInternalMessageSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditInternalMessageValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditInternalMessageValues;
        errors: Record<string, string>;
    };
