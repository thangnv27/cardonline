<?php

class LanguageAdmin extends AdminController {

    private $current_user;

    function __construct() {
        parent::__construct();
        $this->current_user = UserAdmin::checkLogin();
    }

    function index() {
        if ($this->current_user['capability']['languages']['view'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        ## Bulk Actions
        $request = $this->getRequest();

        ## List table
        $table = new Table(Language::$phrases['page']['language']['title.index']);
        $columns = array(
            'col_id' => 'ID',
            'col_flag' => Language::$phrases['page']['language']['flag'],
            'col_name' => Language::$phrases['page']['language']['name'],
            'col_code' => Language::$phrases['page']['language']['code'],
            'col_iso' => Language::$phrases['page']['language']['iso'],
            'col_locale' => Language::$phrases['page']['language']['locale'],
            'col_status' => Language::$phrases['context']['status'],
        );
        $row = "";
        $table->add_columns($columns);

        //Get the records registered in the prepare_items method
        $records = $this->model->getLanguages($request->get('s'));

        //Loop for each record
        if (is_array($records) and !empty($records)) {
            foreach ($records as $rec) {

                //Open the line
                $row .= '<tr id="row_' . $rec->ID . '">';
                foreach ($columns as $field => $title) {
                    $class = "class='$field column-$field' ";
                    $style = "";
                    $attributes = $class . $style;

                    $active_link = DASHBOARD_URL . '/language/' . $rec['id'] . '/active';

                    //Display the cell
                    switch ($field) {
                        case "col_id":
                            $row .= '<td ' . $attributes . '>' . $rec['id'] . '</td>';
                            break;
                        case "col_flag":
                            $row .= '<td ' . $attributes . '>';
                            if ($rec['flag'] != "") {
                                $row .= '<img src="' . $rec['flag'] . '" width="16" height="16" />';
                            }
                            $row .= '</td>';
                            break;
                        case "col_name":
                            $row .= '<td ' . $attributes . '>' . $rec['english_name'] . '</td>';
                            break;
                        case "col_code":
                            $row .= '<td ' . $attributes . '>' . $rec['code'] . '</td>';
                            break;
                        case "col_iso":
                            $row .= '<td ' . $attributes . '>' . $rec['iso'] . '</td>';
                            break;
                        case "col_locale":
                            $row .= '<td ' . $attributes . '>' . $rec['locale'] . '</td>';
                            break;
                        case "col_status":
                            $row .= '<td ' . $attributes . '>';
                            if ($rec['active'] == 1) {
                                $row .= '<span class="label label-warning">Actived</span>';
                            } else {
                                if ($this->current_user['capability']['languages']['edit'] == 1)
                                    $row .= '<a href="' . $active_link . '" class="btn btn-info btn-xs">' . Language::$phrases['action']['active'] . '</a> ';
                            }
                            $row .= '</td>';
                            break;
                    }
                }

                //Close the line
                $row .= '</tr>';
            }
        }

        $table->add_rows($row);

        $this->render("language/index", array(
            'title' => Language::$phrases['page']['language']['title.index'],
            'table' => $table->createView(),
        ));
    }

    function active($id) {
        // Check permission
        if ($this->current_user['capability']['languages']['edit'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $languages = $this->model->getLanguageByID($id);
        $url = DASHBOARD_URL . '/language/';
        if (count($languages) <= 0) {
            $this->redirect($url);
        } else {
            $this->model->active($id);
            $this->getSession()->setFlash('success', Language::$phrases['message']['active_success']);
            $this->redirect($url);
        }
    }

}
