import { CreateEditNewsValues } from './CreateEditNewsValues';

export type CreateEditNewsSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditNewsValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditNewsValues;
        errors: Record<string, string>;
    };
