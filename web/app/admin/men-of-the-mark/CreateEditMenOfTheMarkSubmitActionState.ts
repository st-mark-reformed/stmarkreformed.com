import { CreateEditMenOfTheMarkValues } from './CreateEditMenOfTheMarkValues';

export type CreateEditMenOfTheMarkSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditMenOfTheMarkValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditMenOfTheMarkValues;
        errors: Record<string, string>;
    };
