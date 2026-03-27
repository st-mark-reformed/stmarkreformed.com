import { CreateEditSeriesValues } from './CreateEditSeriesValues';

export type CreateEditSeriesSubmitActionState =
    | {
        ok: true;
        success: boolean;
        values: CreateEditSeriesValues;
    }
    | {
        ok: false;
        success: boolean;
        values: CreateEditSeriesValues;
        errors: Record<string, string>;
    };
