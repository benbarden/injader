/*
  Injader - Content management for everyone
  Copyright (c) 2005-2009 Ben Barden
  Please go to http://www.injader.com if you have questions or need help.

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

  // ** Special initialisation script for WYSIWYG editor ** //

//document.write('<script id="__init_script" defer="true" src="//[]"><\/script>');
  document.write('<script id="__init_script" defer="true"><\/script>');
  function registerInit(callback) {
    /* for Mozilla */
    if (document.addEventListener) {
      document.addEventListener("DOMContentLoaded", callback, false);
    }
    /* for Internet Explorer */
    if (document.getElementById) {
      var deferScript = document.getElementById('__init_script');
      if (deferScript) {
        deferScript.onreadystatechange = function() {
          if (this.readyState == 'complete') {
            callback();
          }
        };
        /* check whether script has already completed */
        deferScript.onreadystatechange();
        /* clear reference to prevent leaks in IE */
        deferScript = null;
      }
    }
    /* for other browsers */
    window.onload = callback;
  }
