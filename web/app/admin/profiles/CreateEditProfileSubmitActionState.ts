import { CreateEditProfileValues } from './CreateEditProfileValues';

export type CreateEditProfileSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditProfileValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditProfileValues;
        errors: Record<string, string>;
    };
