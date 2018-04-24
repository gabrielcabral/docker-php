/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//Date (dd . mm[ . YYYY])
jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-eu-pre": function(date) {
        var date = date.replace(" ", "");

        if (date.indexOf('.') > 0) {
            /*date a, format dd.mn.(yyyy) ; (year is optional)*/
            var eu_date = date.split('.');
        } else {
            /*date a, format dd/mn/(yyyy) ; (year is optional)*/
            var eu_date = date.split('/');
        }

        /*year (optional)*/
        if (eu_date[2]) {
            var year = eu_date[2];
        } else {
            var year = 0;
        }

        /*month*/
        var month = eu_date[1];
        if (month.length == 1) {
            month = 0 + month;
        }

        /*day*/
        var day = eu_date[0];
        if (day.length == 1) {
            day = 0 + day;
        }

        return (year + month + day) * 1;
    },
    "date-eu-asc": function(a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "date-eu-desc": function(a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});

// Date (dd/mm/YYY hh:ii:ss) 
jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-euro-pre": function(a) {
        if ($.trim(a) != '') {
            var frDatea = $.trim(a).split(' ');
            var frTimea = frDatea[1].split(':');
            var frDatea2 = frDatea[0].split('/');
            var x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;
        } else {
            var x = 10000000000000; // = l'an 1000 ...
        }

        return x;
    },
    "date-euro-asc": function(a, b) {
        return a - b;
    },
    "date-euro-desc": function(a, b) {
        return b - a;
    }
});

//Date (dd/mm/YY)
jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function(a) {
		if(a == "" || a == undefined)
			return 0;
			
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },
    "date-uk-asc": function(a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "date-uk-desc": function(a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});

// File size 
jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "file-size-pre": function(a) {
        var x = a.substring(0, a.length - 2);

        var x_unit = (a.substring(a.length - 2, a.length) == "MB" ?
                1000 : (a.substring(a.length - 2, a.length) == "GB" ? 1000000 : 1));

        return parseInt(x * x_unit, 10);
    },
    "file-size-asc": function(a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "file-size-desc": function(a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});

// Commas for decimal place
jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "numeric-comma-pre": function(a) {
        var x = (a == "-") ? 0 : a.replace(/,/, ".");
        return parseFloat(x);
    },
    "numeric-comma-asc": function(a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "numeric-comma-desc": function(a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});

//Checkbox data 
$.fn.dataTableExt.afnSortData['dom-checkbox'] = function(oSettings, iColumn)
{
    var aData = [];
    $('td:eq(' + iColumn + ') input', oSettings.oApi._fnGetTrNodes(oSettings)).each(function() {
        aData.push(this.checked === true ? "1" : "0");
    });
    return aData;
};

//Input element 
$.fn.dataTableExt.afnSortData['dom-text'] = function(oSettings, iColumn)
{
    var aData = [];
    $('td:eq(' + iColumn + ') input', oSettings.oApi._fnGetTrNodes(oSettings)).each(function() {
        aData.push(this.value);
    });
    return aData;
};

jQuery.fn.dataTableExt.aTypes.unshift(
    function ( sData )
    {
        if (sData !== null && sData.match(/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/(19|20|21)\d\d$/))
        {
            return 'date-uk';
        }
        return null;
    }
);
    
jQuery.fn.dataTableExt.aTypes.unshift(
    function ( sData )
    {
        var sValidChars = "0123456789,.";
        var Char;
        var bDecimal = false;
        var iStart=0;
 
        /* Negative sign is valid -  the number check start point */
        if ( sData.charAt(0) === '-' ) {
            iStart = 1;
        }
          
        /* Check the numeric part */
        for ( i=iStart ; i<sData.length ; i++ )
        {
            Char = sData.charAt(i);
            if (sValidChars.indexOf(Char) == -1)
            {
                return null;
            }
        }
          
        return 'numeric-comma';
    }
);