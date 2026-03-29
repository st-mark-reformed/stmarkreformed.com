import { Option } from '../../Forms/SearchableDropdown';
import RequestFactory from '../../../api/request/RequestFactory';

export default async function GetProfilesDropdownList (): Promise<Option[]> {
    const response = await RequestFactory().makeWithoutToken({
        uri: '/admin/profiles/dropdown-list',
        cacheSeconds: 0,
    });

    return response.json as unknown as Option[];
}
