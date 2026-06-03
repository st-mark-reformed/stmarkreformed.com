import { CreateEditPastorsPageValues } from './CreateEditPastorsPageValues';

export type CreateEditPastorsPageSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditPastorsPageValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditPastorsPageValues;
        errors: Record<string, string>;
    };
