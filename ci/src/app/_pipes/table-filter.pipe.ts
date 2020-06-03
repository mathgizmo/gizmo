import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'tableFilter'
})
export class TableFilterPipe implements PipeTransform {

  transform(list: any[], filters: Object) {
    const keys       = Object.keys(filters).filter(key => filters[key]);
    const filterItems = item => keys.every( (key) => isNaN(item[key]) ?
        (item[key].toLowerCase()).includes(filters[key].toLowerCase()) : item[key] === filters[key]);
    return keys.length ? list.filter(filterItems) : list;
  }

}
