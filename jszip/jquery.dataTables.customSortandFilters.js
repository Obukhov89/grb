/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
  /**
   * Custom methods for correct bandwidth sorting
   */
  "bandwidth-pre": function (bandwidth) {
    bandwidth = (jQuery(bandwidth).text()) ? jQuery(bandwidth).text() : bandwidth;
    var bytes;
    if (bandwidth === '<1KB') {
      convertedRowValue = 1;
    } else {
      var scale = bandwidth.substr(bandwidth.length - 2, 2);
      switch (scale) {
        case 'KB':
          bytes = parseFloat(bandwidth)*1024;
          break;
        case 'MB':
          bytes = parseFloat(bandwidth)*1048576;
          break;
      }
    }
    return bytes;
  },
  "bandwidth-asc": function (a, b) {
    return a - b;
  },
  "bandwidth-desc": function (a, b) {
    return b - a;
  }
});

jQuery.extend($.fn.dataTableExt.afnFiltering.push(
    function (oSettings, aData, iDataIndex) {
      var inputFilters = [
        {iColumn: 1, elementId: 'bandwidth_filter', type: 'bandwidth' },
		{iColumn: 2, elementId: 'count_filter', type: 'count' }
      ];
      var match = true;
      for (i = 0; i < inputFilters.length; i++) {
	  var value = jQuery('#' + inputFilters[i].elementId).val();
        switch (inputFilters[i].type) {
          case 'bandwidth':
            if (value && match) {
              bandwidthFilter(value, aData[inputFilters[i].iColumn]);
            }
            break;
		case 'count':
            if (value && match) {
              countFilter(value, aData[inputFilters[i].iColumn]);
            }
            break;
        }
      }
     
      function bandwidthFilter(searchValue, rowValue) {
        var compareChar = searchValue.charAt(0);
        var compareScale = searchValue.charAt(searchValue.length - 1);
        var compareValue = isNaN(parseFloat(compareScale)) ? parseFloat(searchValue.substr(1, searchValue.length - 2)) : parseFloat(searchValue.substr(1, searchValue.length - 1));
        switch (compareScale) {
          case 'm':
            compareValue *= 1048576;
            break;
          default:
            compareValue *= 1024;
        }
        rowValue = (jQuery(rowValue).text()) ? jQuery(rowValue).text() : rowValue;
        var convertedRowValue;
        if (rowValue === '<1KB') {
          convertedRowValue = 1;
        } else {
          var rowValueScale = rowValue.substr(rowValue.length - 2, 2);
          switch (rowValueScale) {
            case 'KB':
              convertedRowValue = parseFloat(rowValue)*1024;
              break;
            case 'MB':
              convertedRowValue = parseFloat(rowValue)*1048576;
              break;
          }
        }
        match = false;
        switch (compareChar) {
          case '<':
            if (compareValue > convertedRowValue) match = true;
            break;
          case '>':
            if (compareValue < convertedRowValue) match = true;
            break;
          case '=':
            if (compareValue == convertedRowValue) match = true;
            break;
          default:
            match = false;
        }
      }
	  
	  function countFilter(searchValue, rowValue) {
        var compareChar = searchValue.charAt(0);
        var compareValue = parseFloat(searchValue.substr(1, searchValue.length - 1));
        rowValue = (jQuery(rowValue).text()) ? jQuery(rowValue).text() : rowValue;
        match = false;
        switch (compareChar) {
          case '<':
            if (compareValue > rowValue) match = true;
            break;
          case '>':
            if (compareValue < rowValue) match = true;
            break;
          case '=':
            if (compareValue == rowValue) match = true;
			break;
          default:
            if (searchValue == rowValue) match = true;
        }
      }

   
      return match;
    }
)
);
