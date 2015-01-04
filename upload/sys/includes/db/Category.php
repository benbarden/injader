<?php
/*
  Injader
  Copyright (c) 2005-2015 Ben Barden


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

class Category extends Helper
{
    public function getTopLevel()
    {
        return $this->ResultQuery("
            SELECT * FROM {IFW_TBL_CATEGORIES}
            WHERE parent_id IS NULL
            ORDER BY name
        ");
    }

    public function getByParent($parentId)
    {
        $parentId = (int) $parentId;
        return $this->ResultQuery("
            SELECT * FROM {IFW_TBL_CATEGORIES}
            WHERE parent_id = $parentId
            ORDER BY name
        ");
    }
}
