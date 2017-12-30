<?php

class UserGroupAdmin extends AdminController {

    private $current_user;

    function __construct() {
        parent::__construct();
        $this->current_user = UserAdmin::checkLogin();
    }

    function index() {
        if ($this->current_user['capability']['userGroups']['view'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        ## List table
        $table = new Table(Language::$phrases['page']['usergroup']['title.index']);
        $columns = array(
            'col_cbox' => '<input type="checkbox" id="checkall" />',
            'col_id' => 'ID',
            'col_name' => Language::$phrases['page']['usergroup']['name'],
            'col_role' => Language::$phrases['page']['usergroup']['role'],
            'col_options' => Language::$phrases['context']['options'],
        );
        $row = "";
        $table->add_columns($columns);

        //Get the records registered in the prepare_items method
        $records = $this->model->getUserGroups();

        //Loop for each record
        if (is_array($records) and !empty($records)) {
            foreach ($records as $rec) {

                //Open the line
                $row .= '<tr id="row_' . $rec->ID . '">';
                foreach ($columns as $field => $title) {
                    $class = "class='$field column-$field' ";
                    $style = "";
                    $attributes = $class . $style;

                    $permission_link = DASHBOARD_URL . '/userGroup/' . $rec['id'] . '/permission';
                    $edit_link = DASHBOARD_URL . '/userGroup/' . $rec['id'] . '/edit';

                    //Display the cell
                    switch ($field) {
                        case "col_cbox":
                            $row .= '<td ' . $attributes . '><input type="checkbox" name="item[]" value="' . $rec['id'] . '" /></td>';
                            break;
                        case "col_id":
                            $row .= '<td ' . $attributes . '>' . $rec['id'] . '</td>';
                            break;
                        case "col_role":
                            $row .= '<td ' . $attributes . '>' . $rec['role'] . '</td>';
                            break;
                        case "col_name":
                            $row .= '<td ' . $attributes . '><a href="' . $edit_link . '">' . $rec['name'] . '</a></td>';
                            break;
                        case "col_options":
                            $row .= '<td ' . $attributes . '>';
                            if ($this->current_user['capability']['userGroups']['edit'] == 1)
                                $row .= '<a href="' . $edit_link . '" class="btn btn-primary btn-xs">' . Language::$phrases['action']['edit'] . '</a> ';
                            if ($this->current_user['capability']['userGroups']['permission'] == 1)
                                $row .= '<a href="' . $permission_link . '" class="btn btn-warning btn-xs">' . Language::$phrases['action']['permission'] . '</a> ';
                            $row .= '</td>';
                            break;
                    }
                }

                //Close the line
                $row .= '</tr>';
            }
        }

        $table->add_rows($row);

        $this->render("usergroup/index", array(
            'title' => Language::$phrases['page']['usergroup']['title.index'],
            'table' => $table->createView(),
        ));
    }

    function permission($id) {
        $group = $this->model->getGroupByID($id);
        $url = DASHBOARD_URL . "/userGroup/";
        if (empty($group)) {
            $this->redirect($url);
        } elseif ($this->current_user['capability']['userGroups']['permission'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $capability = $request->get('capability');
            $groupCapability = @unserialize($group[0]['capability']);
            foreach ($groupCapability as $fn => $act) {
                foreach ($act as $k => $v) {
                    if (!array_key_exists($fn, $capability) or !array_key_exists($k, $capability[$fn])) {
                        $groupCapability[$fn][$k] = 0;
                    } else {
                        $groupCapability[$fn][$k] = 1;
                    }
                }
            }
            $this->model->updateCapability($groupCapability, $id);
            $this->redirect($request->getCurrentRquestUrl());
        }

        $this->render("usergroup/permission", array(
            'title' => Language::$phrases['page']['usergroup']['title.permission'],
            'group' => $group[0],
        ));
    }

    function edit($id) {
        $group = $this->model->getGroupByID($id);
        $url = DASHBOARD_URL . "/userGroup/";
        if (empty($group)) {
            $this->redirect($url);
        } elseif ($this->current_user['capability']['userGroups']['edit'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }


        $title = Language::$phrases['page']['usergroup']['title.edit'];
        $form = new Form($title, array(
            'action' => '',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));
        $form->add('name', 'text', array(
            'label' => Language::$phrases['page']['usergroup']['name'],
            'data' => $group[0]['name'],
            'attr' => array(
                'required' => true
            )
        ));

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $name = $request->get('name');

            $msg = "";
            if ($name == "") {
                $msg .= "<p>" . Language::$phrases['message']['name.empty'] . "</p>";
            } elseif ($name != $group[0]['name'] and $this->model->isNameExists($name)) {
                $msg .= "<p>" . Language::$phrases['message']['name.exists'] . "</p>";
            }

            if ($msg != "") {
                $this->getSession()->setFlash('warning', $msg);
            } else {
                $this->model->update($name, $id);
                $this->getSession()->setFlash('success', Language::$phrases['message']['update_success']);
                $this->redirect($request->getCurrentRquestUrl());
            }
        }

        $this->render("usergroup/edit", array(
            'title' => $title,
            'formview' => $form->createView(),
            'group' => $group[0],
        ));
    }

}
