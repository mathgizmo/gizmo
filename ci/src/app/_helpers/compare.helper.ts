import * as moment from 'moment/moment';

export function compare(a: number | string, b: number | string, isAsc: boolean, isDate = false) {
    if (isDate) {
        const aDate = a ? moment(a, 'YYYY-MM-DD hh:mm A') : moment(0);
        const bDate = b ? moment(b, 'YYYY-MM-DD hh:mm A') : moment(0);
        return (aDate.isBefore(bDate) ? -1 : 1) * (isAsc ? 1 : -1);
    } else {
        if (typeof a === 'string' || typeof b === 'string') {
            a = ('' + a).toLowerCase();
            b = ('' + b).toLowerCase();
        }
        return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
    }
}
