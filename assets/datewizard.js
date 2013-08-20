/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
 
 
/**
 * Class Datewizard
 *
 * @copyright  Simon Wohler 2013
 * @author     Simon Wohler <http://www.bekanntmacher.ch> 
 * @author     Leo Feyer <http://contao.org>
 */ 
var Datewizard =
{

    dateWizard: function(el, command, id) {
    var list = $(id);
    var parent = $(el).getParent('li');
    var items = list.getChildren();
    Backend.getScrollOffset();

    switch (command) {
        case 'copy':
            var clone = parent.clone(true).inject(parent, 'before');

            var d = new Date();
            var n = d.getTime();

            var uniqueId = "dateWizard-input-" + n;
            while($(uniqueId)) {
                uniqueId = "dateWizard-input-" + n;
            }

            clone.getFirst('input').setAttribute('id', uniqueId);

            if (input = parent.getFirst('input')) {
                clone.getFirst('input').value = input.value;
            }
            break;
        case 'up':
            if (previous = parent.getPrevious('li')) {
                parent.inject(previous, 'before');
            } else {
                parent.inject(list, 'bottom');
            }
            break;
        case 'down':
            if (next = parent.getNext('li')) {
                parent.inject(next, 'after');
            } else {
                parent.inject(list.getFirst('li'), 'before');
            }
            break;
        case 'delete':
            if (items.length > 1) {
                parent.destroy();
            }
            break;
    }

    rows = list.getChildren();
    var tabindex = 1;

    for (var i=0; i<rows.length; i++) {
        if (input = rows[i].getFirst('input[type="text"]')) {
            input.set('tabindex', tabindex++);
        }
    }
}

}