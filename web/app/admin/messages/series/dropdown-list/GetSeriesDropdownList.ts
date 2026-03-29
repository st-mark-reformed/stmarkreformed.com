import RequestFactory from '../../../../api/request/RequestFactory';
import { Option } from '../../../Forms/SearchableDropdown';

export default async function GetSeriesDropdownList () {
    const response = await RequestFactory().makeWithoutToken({
        uri: '/admin/series/dropdown-list',
        cacheSeconds: 0,
    });

    return response.json as unknown as Option[];
}
