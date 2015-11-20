<?php
/*
 * Xibo - Digital Signage - http://www.xibo.org.uk
 * Copyright (C) 2009-14 Daniel Garner
 *
 * This file is part of Xibo.
 *
 * Xibo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Xibo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Xibo.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('XIBO') or die("Sorry, you are not allowed to directly access this page.<br /> Please press the back button in your browser.");

class Category extends Data
{
    /**
     * Adds a category
     * @param string $category
     * @return <type>
     */
    public function Add($category)
    {
        try {
            $dbh = PDOConnect::init();

            if ($category == '')
                $this->ThrowError(__('All fields must be filled in'));
    

            $sth = $dbh->prepare('INSERT INTO category (category VALUES (:category)');
            $sth->execute(array(
                    'category' => $category
                ));
            
            return true;  
        }
        catch (Exception $e) {
            
            Debug::LogEntry('error', $e->getMessage());
        
            if (!$this->IsError())
                return $this->SetError(25000, __('Cannot add this category.'));
        
            return false;
        }
    }

    /**
     * Edits a Category
     * @param <type> $categoryID
     * @param <type> $category
     * @return <type>
     */
    public function Edit($categoryID, $category)
    {
        try {
            $dbh = PDOConnect::init();

            if ($category == '')
                $this->ThrowError(__('All fields must be filled in'));
    
    
            $sth = $dbh->prepare('
                UPDATE category SET category = :category WHERE categoryID = :categoryid');

            $sth->execute(array(
                    'category' => $category,
                    'categoryid' => $categoryID
                ));
            
            return true;
        }
        catch (Exception $e) {
            
            Debug::LogEntry('error', $e->getMessage());
        
            if (!$this->IsError())
                return $this->SetError(25000, __('Cannot edit this category.'));
        
            return false;
        }
    }

    /**
     * Deletes a Category
     * @param <type> $categoryID
     * @return <type>
     */
    public function Delete($categoryID)
    {
        try {
            $dbh = PDOConnect::init();
        
            $sth = $dbh->prepare('DELETE FROM category WHERE categoryID = :categoryid');
            $sth->execute(array(
                    'categoryid' => $categoryID
                ));

            return true;  
        }
        catch (Exception $e) {
            
            Debug::LogEntry('error', $e->getMessage());
        
            if (!$this->IsError())
                $this->SetError(25000, __('Cannot delete this category.'));
        
            return false;
        }
    }
}
?>