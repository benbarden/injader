<?php
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

  require 'InjaderPage.php';
  $IJP = new InjaderPage;
  $strHTML = <<<PageContent
<h1 style="text-align: center;">Injader Install Wizard</h1>
<table>
  <tr>
    <td style="padding: 8px; vertical-align: top; width: 48%;">
      <h2 style="background-color: #000; color: #fff; margin-top: 0px; padding: 4px;">Setting up a brand new site</h2>
      <p><b>IMPORTANT: If you install over an existing site, all of your content will be deleted - so be careful!</b></p>
      <ol>
      <li>Read the <a href="http://help.injader.com/installation">Installation Overview</a>. Ensure that you haven't missed any steps.</li>
      <li>When you get to the step that asks you to run the installation wizard, you're ready to go. <a href="install.php">Click here to run the installation wizard</a>. Follow the on-screen prompts and you'll be up and running in no time.</li>
      </ol>
    </td>
    <td style="border-left: 2px solid #000; padding: 8px; vertical-align: top; width: 48%;">
      <h2 style="background-color: #000; color: #fff; margin-top: 0px; padding: 4px;">Upgrading an existing site</h2>
      <ol>
      <li>Read the <a href="http://help.injader.com/upgrading">Upgrade Guide</a>. Ensure you haven't missed any steps.</li>
      <li><a href="upgrade-intro.php">Run the upgrade script</a>.</li>
      </ol>
    </td>
  </tr>
</table>
<p style="text-align: center;">Need help? Post in our <a href="http://forums.injader.com">Help Forum</a>.</p>

PageContent;
  $IJP->Display($strHTML, "Injader Install Wizard");
?>