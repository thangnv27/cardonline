<?php

class CodeAdmin extends AdminController {

    private $current_user;

    function __construct() {
        parent::__construct();
        $this->current_user = UserAdmin::checkLogin();
    }

    function index() {
        if ($this->current_user['capability']['code']['view'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        ## Bulk Actions
        $request = $this->getRequest();
        $action = $request->get('action');
        $url = DASHBOARD_URL . '/code/';
        switch ($action) {
            case 'move2trash':
                // Check permission
                if ($this->current_user['capability']['code']['edit'] == 0) {
                    Debug::throwException(Language::$phrases['message']['error_occur'], null);
                }

                $checked = $request->get('item');
                if (count($checked) > 0) {
                    $code_ID = implode(", ", $checked);
                    $this->model->move2trashBulk("id IN ($code_ID)");
                    $this->getSession()->setFlash('success', Language::$phrases['message']['move2trash_success']);
                }
                $this->redirect($url);
                break;
            case 'publish':
                // Check permission
                if ($this->current_user['capability']['code']['edit'] == 0) {
                    Debug::throwException(Language::$phrases['message']['error_occur'], null);
                }

                $checked = $request->get('item');
                if (count($checked) > 0) {
                    $code_ID = implode(", ", $checked);
                    $this->model->publishBulk("id IN ($code_ID)");
                    $this->getSession()->setFlash('success', Language::$phrases['message']['publish_success']);
                }
                $this->redirect($url);
                break;
            default:
                break;
        }

        ## List table
        $table = new Table(Language::$phrases['code']['code']['title.index']);
        $columns = array(
            'col_cbox' => '<input type="checkbox" id="checkall" />',
            'col_id' => 'ID',
            'col_title' => Language::$phrases['context']['title'],
            'col_options' => Language::$phrases['context']['options'],
        );
        $row = "";
        $table->add_columns($columns);

        $where = array();
        
        if (in_array($request->get('status'), array('trashed', 'draft', 'published'))) {
            $where = "code_status='" . $request->get('status') . "'";
        } elseif($request->get('type') == 'used'){
            $where = "user_id > 0";
        } elseif($request->get('type') == 'noused'){
            $where = "user_id = 0 AND code_status = 'published'";
        } else {
            $search_query = $request->get('s');
            if (!empty($search_query)) {
                $where = "code_status IN ('draft','published') AND (title LIKE '%$search_query%' OR "
                        . "slug LIKE '%$search_query%' OR content LIKE '%$search_query%' OR "
                        . "posted_date LIKE '%$search_query%')";
            } else {
                $where = "code_status IN ('draft','published')";
            }
        }

        // Pagination
        $currentURL = trailingslashit($request->getCurrentRquestUrl());
        if (count($request->all()) > 0) {
            $currentURL = $request->getCurrentRquestUrl();
        }
        $limit = 50;
        $codes = new Pagenavi($currentURL, $request->get('page'), $limit);
        $start = $codes->start($limit);
        $countRecords = $this->model->countPages($where);
        $table->add_pagenavi($codes->pageList($countRecords));

        //Get the records registered in the prepare_items method
        $records = $this->model->getPages($start, $limit, $where);

        //Loop for each record
        if (is_array($records) and !empty($records)) {
            foreach ($records as $rec) {
                
                if ($rec['user_id'] == '0') {
                    $code_status =  'info';
                }else{
                    $code_status =  'danger';
                }
                //Open the line
                $row .= '<tr id="row_' . $rec['id'] . '" class="'.$code_status.'">';
                foreach ($columns as $field => $title) {
                    $class = "class='$field column-$field' ";
                    $style = "";
                    $attributes = $class . $style;

                    $edit_link = DASHBOARD_URL . '/code/' . $rec['id'] . '/edit';
                    $delete_link = DASHBOARD_URL . '/code/' . $rec['id'] . '/delete';
                    $publish_link = DASHBOARD_URL . '/code/' . $rec['id'] . '/publish';

                    //Display the cell
                    switch ($field) {
                        case "col_cbox":
                            $row .= '<td ' . $attributes . '><input type="checkbox" name="item[]" value="' . $rec['id'] . '" /></td>';
                            break;
                        case "col_id":
                            $row .= '<td ' . $attributes . '>' . $rec['id'] . '</td>';
                            break;
                        case "col_title":
                            $row .= '<td ' . $attributes . '><a href="' . $edit_link . '">' . $rec['code'] . '</a></td>';
                            break;
                        case "col_options":
                            $row .= '<td ' . $attributes . '>';
                            if ($this->current_user['capability']['code']['edit'] == 1)
                                $row .= '<a href="' . $edit_link . '" class="btn btn-primary btn-xs">' . Language::$phrases['action']['edit'] . '</a> ';
                            if ($this->current_user['capability']['code']['delete'] == 1)
                                $row .= '<a href="' . $delete_link . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . Language::$phrases['action']['delete.confirm'] . '\');">' . Language::$phrases['action']['delete'] . '</a> ';
                            if ($this->current_user['capability']['code']['edit'] == 1 and $rec['code_status'] == 'draft')
                                $row .= '<a href="' . $publish_link . '" class="btn btn-warning btn-xs">' . Language::$phrases['action']['publish'] . '</a> ';
                            $row .= '</td>';
                            break;
                    }
                }

                //Close the line
                $row .= '</tr>';
            }
        }

        $table->add_rows($row);

        $this->render("code/index", array(
            'title' => Language::$phrases['context']['code'],
            'table' => $table->createView(),
            'request' => $request,
        ));
    }

    function addnew() {
        // Check permission
        if ($this->current_user['capability']['code']['create'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $title = Language::$phrases['context']['code'];

        $request = $this->getRequest();
        $content = $request->get('content');
        $code_status = $request->get('code_status');

        $form = new Form();
        $form->add('content', 'textarea', array(
                    'label' => Language::$phrases['context']['code'],
                    'data' => $content,
                    'attr' => array(
                        'rows' => 10
                    )
                ))
                ->add('code_status', 'choice', array(
                    'label' => Language::$phrases['context']['status'],
                    'choices' => Utils::getPostStatus(),
                    'data' => $code_status,
        ));

        if ($request->getMethod() == 'POST') {
            $msg = "";
            if ($content == "") {
                $msg .= Language::$phrases['context']['pages']['content.error.empty'];
            }
            if ($msg != "") {
                $this->getSession()->setFlash('warning', $msg);
            } else {
                $str_arr = explode("\n", $content);
                foreach ($str_arr as $code_content) {
                    $id = $this->model->create(array(
                    'code' => $code_content,
                    'code_status' => $code_status,
                    ));
                }

                if ($id) {
                    $this->getSession()->setFlash('success', Language::$phrases['message']['create_success']);
                    $this->redirect(DASHBOARD_URL . '/code/' . $id . '/edit');
                }
            }
        }

        $this->render("code/new", array(
            'title' => $title,
            'form' => $form,
            'request' => $request,
        ));
    }

    function edit($id) {
        // Check permission
        if ($this->current_user['capability']['code']['edit'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $code = $this->model->getCodeByID($id);
        if (count($code) <= 0) {
            $this->redirect(DASHBOARD_URL . '/code/');
        } else {
            $code = $code[0];
            $title = Language::$phrases['context']['code'];

            $form = new Form();
            $form->add('content', 'text', array(
                        'label' => Language::$phrases['context']['code'],
                        'data' => $code['code']
                    ))
                    ->add('code_status', 'choice', array(
                        'label' => Language::$phrases['context']['status'],
                        'choices' => Utils::getPostStatus(),
                        'data' => $code['code_status'],
            ));

            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $content = $request->get('content');
                $code_status = $request->get('code_status');
                $msg = "";
                if ($content == "") {
                    $msg .= Language::$phrases['code']['code']['content.error.empty'];
                }
                if ($msg != "") {
                    $this->getSession()->setFlash('warning', $msg);
                } else {
                    $this->model->update(array(
                        'code' => $content,
                        'code_status' => $code_status,
                        ), array(
                        'id' => $id,
                    ));
                    $this->getSession()->setFlash('success', Language::$phrases['message']['update_success']);
                    $this->redirect($request->getCurrentRquestUrl());
                }
            }

            $this->render("code/edit", array(
                'title' => $title,
                'form' => $form,
                'code' => $code,
            ));
        }
    }

    function publish($id) {
        // Check permission
        if ($this->current_user['capability']['code']['edit'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $code = $this->model->getCodeByID($id);
        $url = DASHBOARD_URL . '/code/';
        if (count($code) <= 0) {
            $this->redirect($url);
        } else {
            $this->model->publish($id);
            $this->getSession()->setFlash('success', Language::$phrases['message']['publish_success']);
            $this->redirect($url);
        }
    }

    function delete($id) {
        // Check permission
        if ($this->current_user['capability']['code']['delete'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $code = $this->model->getCodeByID($id);
        $url = DASHBOARD_URL . '/code/';
        if (count($code) <= 0) {
            $this->redirect($url);
        } else {
            $this->model->delete($id);
            $this->getSession()->setFlash('success', Language::$phrases['message']['delete_success']);
            $this->redirect($url);
        }
    }

}
