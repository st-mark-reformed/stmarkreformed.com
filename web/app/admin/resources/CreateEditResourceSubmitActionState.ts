import { CreateEditResourceValues } from './CreateEditResourceValues';

export type CreateEditResourceSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditResourceValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditResourceValues;
        errors: Record<string, string>;
    };
