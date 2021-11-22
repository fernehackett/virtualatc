import * as $ from 'jquery';
import 'jquery-datetimepicker/build/jquery.datetimepicker.full.min.js';
import 'jquery-datetimepicker/build/jquery.datetimepicker.min.css';

export default (function () {

  // $.datetimepicker.setLocale('en');
    if($('.date').length > 0) {
        $('.date').datetimepicker({
            timepicker: false,
            format: 'Y-m-d'
        }).attr('autocomplete', "off");
    }
    if($('.date-time').length > 0) {
        $('.date-time').datetimepicker({
            format: 'Y-m-d H:i:s'
        }).attr('autocomplete', "off");
    }
}())
