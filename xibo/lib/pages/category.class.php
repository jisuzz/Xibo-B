<?php
/*
 * Xibo - Digital Signage - http://www.xibo.org.uk
 * Copyright (C) 2009-2014 Daniel Garner
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

include_once('lib/data/category.data.class.php');

class categoryDAO extends baseDAO
{
    /**
     * Display the category Page
     */
    function displayPage()
    {
        // Configure the theme
        $id = uniqid();
        Theme::Set('id', $id);
        Theme::Set('form_meta', '<input type="hidden" name="p" value="category"><input type="hidden" name="q" value="categoryGrid">');
        Theme::Set('filter_id', 'XiboFilterPinned' . uniqid('filter'));
        Theme::Set('pager', ResponseManager::Pager($id));

        if (Kit::IsFilterPinned('category', 'CategoryFilter')) {
            $pinned = 1;
            $enabled = Session::Get('category', 'filterEnabled');
        }
        else {
            $enabled = 1;
            $pinned = 0;
        }

        $formFields = array();
        $formFields[] = FormManager::AddCombo(
            'filterEnabled',
            __('Enabled'),
            $enabled,
            array(array('enabledid' => 1, 'enabled' => 'Yes'), array('enabledid' => 0, 'enabled' => 'No')),
            'enabledid',
            'enabled',
            NULL,
            'e');

        $formFields[] = FormManager::AddCheckbox('XiboFilterPinned', __('Keep Open'),
            $pinned, NULL,
            'k');

        // Call to render the template
        Theme::Set('header_text', __('Categorys'));
        Theme::Set('form_fields', $formFields);
        Theme::Render('grid_render');
    }

    function actionMenu() {

        return array(
            array('title' => __('Filter'),
                'class' => '',
                'selected' => false,
                'link' => '#',
                'help' => __('Open the filter form'),
                'onclick' => 'ToggleFilterView(\'Filter\')'
            ),
            array('title' => __('Add Category'),
                    'class' => 'XiboFormButton',
                    'selected' => false,
                    'link' => 'index.php?p=category&q=AddForm',
                    'help' => __('Add a new category for use on layouts'),
                    'onclick' => ''
                    )
            );                   
    }

    /**
     * Category Grid
     */
    function CategoryGrid()
    {
        $user 	=& $this->user;
        $response = new ResponseManager();

        setSession('category', 'CategoryFilter', Kit::GetParam('XiboFilterPinned', _REQUEST, _CHECKBOX, 'off'));
        // Show enabled
        $filterEnabled = Kit::GetParam('filterEnabled', _POST, _INT);
        setSession('category', 'filterEnabled', $filterEnabled);

        $rows = $user->CategoryList(array('category'), array('enabled' => $filterEnabled));
        $categorys = array();

        $cols = array(
                array('name' => 'categoryid', 'title' => __('ID')),
                array('name' => 'category', 'title' => __('Category'))
            );
        Theme::Set('table_cols', $cols);

        foreach($rows as $row) {

            // Edit Button
            $row['buttons'][] = array(
                    'id' => 'category_button_edit',
                    'url' => 'index.php?p=category&q=EditForm&categoryid=' . $row['categoryid'],
                    'text' => __('Edit')
                );

            // Delete Button
            $row['buttons'][] = array(
                    'id' => 'category_button_delete',
                    'url' => 'index.php?p=category&q=DeleteForm&categoryid=' . $row['categoryid'],
                    'text' => __('Delete')
                );

            // Add to the rows objects
            $categorys[] = $row;
        }

        Theme::Set('table_rows', $categorys);

        $response->SetGridResponse(Theme::RenderReturn('table_render'));
        $response->Respond();
    }

    /**
     * Category Add
     */
    function AddForm()
    {
        $db 	=& $this->db;
        $user 	=& $this->user;
        $response = new ResponseManager();

        Theme::Set('form_id', 'AddForm');
        Theme::Set('form_action', 'index.php?p=category&q=Add');

        $formFields = array();
        $formFields[] = FormManager::AddText('category', __('Category'), NULL, 
            __('A name for this Category'), 'r', 'required');

        Theme::Set('form_fields', $formFields);

        $response->SetFormRequestResponse(NULL, __('Add Category'), '350px', '250px');
        $response->AddButton(__('Help'), 'XiboHelpRender("' . HelpManager::Link('Category', 'Add') . '")');
        $response->AddButton(__('Cancel'), 'XiboDialogClose()');
        $response->AddButton(__('Save'), '$("#AddForm").submit()');
        $response->Respond();
    }

    /**
     * Category Edit Form
     */
    function EditForm()
    {
        $db 	=& $this->db;
        $user 	=& $this->user;
        $response = new ResponseManager();

        $categoryID   = Kit::GetParam('categoryid', _GET, _INT);

        $SQL = sprintf("SELECT category FROM category WHERE categoryID = %d", $categoryID);

        if (!$result = $db->query($SQL))
        {
            trigger_error($db->error());
            trigger_error(__('Unable to edit this category'), E_USER_ERROR);
        }

        if ($db->num_rows($result) == 0)
            trigger_error(__('Incorrect category id'), E_USER_ERROR);

        $row = $db->get_assoc_row($result);

        $formFields = array();
        $formFields[] = FormManager::AddText('category', __('Category'), Kit::ValidateParam($row['category'], _STRING), 
            __('A name for this Category'), 'r', 'required');

        
        Theme::Set('form_fields', $formFields);

        Theme::Set('form_id', 'CategoryForm');
        Theme::Set('form_action', 'index.php?p=category&q=Edit');
        Theme::Set('form_meta', '<input type="hidden" name="categoryid" value="' . $categoryID . '" >');

        $response->SetFormRequestResponse(NULL, __('Edit Category'), '350px', '250px');
        $response->AddButton(__('Help'), 'XiboHelpRender("' . HelpManager::Link('Template', 'Add') . '")');
        $response->AddButton(__('Cancel'), 'XiboDialogClose()');
        $response->AddButton(__('Save'), '$("#CategoryForm").submit()');
        $response->Respond();
    }

    /**
     * Category Delete Form
     */
    function DeleteForm()
    {
        $db 	=& $this->db;
        $user 	=& $this->user;
        $response = new ResponseManager();

        $categoryid   = Kit::GetParam('categoryid', _GET, _INT);

        // Set some information about the form
        Theme::Set('form_id', 'DeleteForm');
        Theme::Set('form_action', 'index.php?p=category&q=Delete');
        Theme::Set('form_meta', '<input type="hidden" name="categoryid" value="' . $categoryid . '" />');
        Theme::Set('form_fields', array(FormManager::AddMessage(__('Are you sure you want to delete?'))));
        
        $response->SetFormRequestResponse(Theme::RenderReturn('form_render'), __('Delete Category'), '250px', '150px');
        $response->AddButton(__('Help'), 'XiboHelpRender("' . HelpManager::Link('Campaign', 'Delete') . '")');
        $response->AddButton(__('No'), 'XiboDialogClose()');
        $response->AddButton(__('Yes'), '$("#DeleteForm").submit()');
        $response->Respond();
    }

    function Add()
    {
        // Check the token
        if (!Kit::CheckToken())
            trigger_error(__('Sorry the form has expired. Please refresh.'), E_USER_ERROR);
        
        $db 	=& $this->db;
        $user 	=& $this->user;
        $response = new ResponseManager();

        $category = Kit::GetParam('category', _POST, _STRING);

        // Add the category
        $resObject = new Category($db);

        if (!$resObject->Add($category))
            trigger_error($resObject->GetErrorMessage(), E_USER_ERROR);

        $response->SetFormSubmitResponse('New category added');
        $response->Respond();
    }

    function Edit()
    {
        // Check the token
        if (!Kit::CheckToken())
            trigger_error(__('Sorry the form has expired. Please refresh.'), E_USER_ERROR);
        
        $db 	=& $this->db;
        $user 	=& $this->user;
        $response = new ResponseManager();

        $categoryID = Kit::GetParam('categoryid', _POST, _INT);
        $category = Kit::GetParam('category', _POST, _STRING);
        
        // Edit the category
        $resObject = new Category($db);

        if (!$resObject->Edit($categoryID, $category))
            trigger_error($resObject->GetErrorMessage(), E_USER_ERROR);

        $response->SetFormSubmitResponse('Category edited');
        $response->Respond();
    }

    function Delete()
    {
        // Check the token
        if (!Kit::CheckToken())
            trigger_error(__('Sorry the form has expired. Please refresh.'), E_USER_ERROR);
        
        $db 	=& $this->db;
        $user 	=& $this->user;
        $response = new ResponseManager();

        $categoryID = Kit::GetParam('categoryid', _POST, _INT);

        // Remove the category
        $resObject = new Category($db);

        if (!$resObject->Delete($categoryID))
            trigger_error($resObject->GetErrorMessage(), E_USER_ERROR);

        $response->SetFormSubmitResponse('Category deleted');
        $response->Respond();
    }
}
?>