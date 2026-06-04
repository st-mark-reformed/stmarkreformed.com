import { CreateEditHymnOfTheMonthValues } from './CreateEditHymnOfTheMonthValues';

export type CreateEditHymnOfTheMonthSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditHymnOfTheMonthValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditHymnOfTheMonthValues;
        errors: Record<string, string>;
    };
