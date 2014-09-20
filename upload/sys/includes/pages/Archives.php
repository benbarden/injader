<?php
/*
    Injader - Content management for everyone
    Copyright (c) 2005-2010 Ben Barden
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
class pages_Archives extends Helper {
    
    /**
     * Builds the Archives page
     * @param string $param1
     * @param string $param2
     * @return string
     */
    function build($param1 = "", $param2 = "") {
        
        global $CMS;
        
        $strHTML = "";
        
        // Validation
        $param1 = $CMS->FilterNumeric($param1);
        $param2 = $CMS->FilterNumeric($param2);
        
        if (empty($param1) || (empty($param2))) {
            
            // Get archives summary
            $result = $CMS->ART->getArchivesSummary($param1, $param2);
            
            if (is_array($result)) {
                $strHTML = '<h1><a href="'.URL_ROOT.'cms/archives">Archives</a></h1>'."\n";
                $listHTML = "";
                foreach ($result as $item) {
                    $dateDesc  = $item['content_date_desc'];
                    $dateYear  = $item['content_yyyy'];
                    $dateMonth = $item['content_mm'];
                    $dateCount = $item['count'];
                    $dateLink  = URL_ROOT."cms/archives/$dateYear/$dateMonth";
                    if (empty($listHTML)) {
                        $listHTML .= "<ul>\n";
                    }
                    $listHTML .= '<li><a href="'.$dateLink.'">'.$dateDesc.'</a> ('.$dateCount.')</li>'."\n";
                }
                if (!empty($listHTML)) {
                    $listHTML .= "</ul>\n";
                }
                $strHTML .= $listHTML;
            } else {
                $strHTML = '<h1><a href="'.URL_ROOT.'cms/archives">Archives</a></h1>'.
                    "\n".'<p>No content found.</p>';
            }
            
        } else {
            
            // Get archives links
            $result = $CMS->ART->getArchivesContent($param1, $param2);
            
            if (is_array($result)) {
                $strHTML = '<h1><a href="'.URL_ROOT.'cms/archives">Archives</a></h1>'."\n";
                $listHTML = "";
                foreach ($result as $item) {
                    $dateDesc  = $item['content_date_full'];
                    $dateYear  = $item['content_yyyy'];
                    $dateMonth = $item['content_mm'];
                    $itemTitle = $item['title'];
                    $itemLink  = $CMS->PL->ViewArticle($item['id'], $item['content_area_id']);
                    if (empty($listHTML)) {
                        $listHTML .= "<ul>\n";
                    }
                    $listHTML .= '<li>'.$dateDesc.' - <a href="'.$itemLink.'">'.$itemTitle.'</a></li>'."\n";
                }
                if (!empty($listHTML)) {
                    $listHTML .= "</ul>\n";
                }
                $strHTML .= $listHTML;
            } else {
                $strHTML = '<h1><a href="'.URL_ROOT.'cms/archives">Archives</a></h1>'.
                    "\n".'<p>No content found.</p>';
            }
            
        }
        
        return $strHTML;
        
    }
    
}
?>