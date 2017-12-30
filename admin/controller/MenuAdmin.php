<?php

class MenuAdmin extends AdminController {

    private $current_user;

    function __construct() {
        parent::__construct();
        $this->current_user = UserAdmin::checkLogin();
    }

    function index() {
        if ($this->current_user['capability']['menu']['manage'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        ## Bulk Actions
        $request = $this->getRequest();

        ## List table
        $table = new Table(Language::$phrases['page']['menu']['title.index']);
        $columns = array(
            'col_image' => Language::$phrases['page']['menu']['image'],
            'col_title' => Language::$phrases['context']['title'],
            'col_description' => Language::$phrases['page']['menu']['description'],
            'col_options' => Language::$phrases['context']['options'],
        );
        $row = "";
        $table->add_columns($columns);

        //Get the records registered in the prepare_items method
        $records = $this->model->menuRowsTable($request->get('name'));

        //Loop for each record
        if (is_array($records) and !empty($records)) {
            foreach ($records as $rec) {

                //Open the line
                $row .= '<tr id="row_' . $rec->ID . '">';
                foreach ($columns as $field => $title) {
                    $class = "class='$field column-$field' ";
                    $style = "";
                    $attributes = $class . $style;

                    $edit_link = DASHBOARD_URL . '/menu/' . $rec['id'] . '/edit/?name=' . $request->get('name');
                    $delete_link = DASHBOARD_URL . '/menu/' . $rec['id'] . '/delete/?name=' . $rec['name'];

                    //Display the cell
                    switch ($field) {
                        case "col_image":
                            $row .= '<td ' . $attributes . '>';
                            if ($rec['image'] != "") {
                                $row .= '<img src="' . $rec['image'] . '" width="32" height="32" />';
                            }
                            $row .= '</td>';
                            break;
                        case "col_title":
                            $row .= '<td ' . $attributes . '><a href="' . $rec['url'] . '" target="_blank">' . $rec['title'] . '</a></td>';
                            break;
                        case "col_description":
                            $row .= '<td ' . $attributes . '>' . $rec['description'] . '</td>';
                            break;
                        case "col_options":
                            $row .= '<td ' . $attributes . '>';
                            if ($this->current_user['capability']['menu']['manage'] == 1) {
                                $row .= '<a href="' . $edit_link . '" class="btn btn-primary btn-xs">' . Language::$phrases['action']['edit'] . '</a> ';
                                $row .= '<a href="' . $delete_link . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . Language::$phrases['action']['delete.confirm'] . '\');">' . Language::$phrases['action']['delete'] . '</a>';
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

        $this->render("menu/index", array(
            'title' => Language::$phrases['page']['menu']['title.index'],
            'table' => $table->createView(),
        ));
    }

    function addnew() {
        // Check permission
        if ($this->current_user['capability']['menu']['manage'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $title = Language::$phrases['page']['menu']['title.addnew'];
        $url_list = DASHBOARD_URL . '/menu/';

        $request = $this->getRequest();
        $name = $request->get('name');

        if ($name == "") {
            $this->getSession()->setFlash('warning', Language::$phrases['message']['empty_menu_name']);
            $this->redirect($url_list);
        }

        $menu_title = $request->get('title');
        $url = $request->get('menu_url');
        $parent = intval($request->get('parent'));
        $description = $request->get('description');
        $image = $request->get('image');
        $displayorder = ($request->get('displayorder') == "") ? 1 : $request->get('displayorder');

        $form = new Form($title, array(
            'action' => '',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));
        $form->add("name", "hidden", array(
                    'data' => $name,
                    'attr' => array(
                        'required' => true
                    )
                ))
                ->add('title', 'text', array(
                    'label' => Language::$phrases['context']['title'],
                    'data' => $menu_title,
                ))
                ->add('menu_url', 'text', array(
                    'label' => Language::$phrases['page']['menu']['url'],
                    'data' => $url,
                ))
                ->add('parent', 'choice', array(
                    'label' => Language::$phrases['page']['menu']['parent'],
                    'choices' => $this->model->menuOptions($name),
                    'data' => $parent,
                ))
                ->add('description', 'textarea', array(
                    'label' => Language::$phrases['page']['menu']['description'],
                    'data' => $description,
                ))
                ->add('image', 'upload', array(
                    'label' => Language::$phrases['page']['menu']['image'],
                    'data' => $image,
                    'btn' => array(
                        'onclick' => "openFileDialog('image')"
                    )
                ))
                ->add('displayorder', 'text', array(
                    'label' => Language::$phrases['page']['menu']['displayorder'],
                    'data' => $displayorder,
        ));

        if ($request->getMethod() == 'POST') {
            $id = $this->model->create(array(
                'lang_code' => Language::$lang_content,
                'name' => $name,
                'title' => $menu_title,
                'url' => $url,
                'parent' => $parent,
                'description' => $description,
                'image' => $image,
                'displayorder' => $displayorder,
            ));
            if ($id) {
                $this->getSession()->setFlash('success', Language::$phrases['message']['create_success']);
                $this->redirect($url_list . "/?name=" . $name);
            }
        }

        $this->render("menu/new", array(
            'title' => $title,
            'formview' => $form->createView(),
        ));
    }

    function edit($id) {
        // Check permission
        if ($this->current_user['capability']['menu']['manage'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $menus = $this->model->getMenuByID($id);
        if (count($menus) <= 0) {
            $this->redirect(DASHBOARD_URL . '/menu/');
        } else {
            $menu = $menus[0];
            $title = Language::$phrases['page']['menu']['title.edit'];

            $form = new Form($title, array(
                'action' => '',
                'method' => 'post',
                'class' => 'form-horizontal'
            ));
            $form->add('title', 'text', array(
                        'label' => Language::$phrases['context']['title'],
                        'data' => $menu['title'],
                    ))
                    ->add('menu_url', 'text', array(
                        'label' => Language::$phrases['page']['menu']['url'],
                        'data' => $menu['url'],
                    ))
                    ->add('parent', 'choice', array(
                        'label' => Language::$phrases['page']['menu']['parent'],
                        'choices' => $this->model->menuOptions($menu['name']),
                        'data' => $menu['parent'],
                    ))
                    ->add('description', 'textarea', array(
                        'label' => Language::$phrases['page']['menu']['description'],
                        'data' => $menu['description'],
                    ))
                    ->add('image', 'upload', array(
                        'label' => Language::$phrases['page']['menu']['image'],
                        'data' => $menu['image'],
                        'btn' => array(
                            'onclick' => "openFileDialog('image')"
                        )
                    ))
                    ->add('displayorder', 'text', array(
                        'label' => Language::$phrases['page']['menu']['displayorder'],
                        'data' => $menu['displayorder'],
            ));

            $request = $this->getRequest();
            if ($request->getMethod() == 'POST') {
                $menu_title = $request->get('title');
                $url = $request->get('menu_url');
                $parent = intval($request->get('parent'));
                $description = $request->get('description');
                $image = $request->get('image');
                $displayorder = ($request->get('displayorder') == "") ? 1 : $request->get('displayorder');

                $this->model->update(array(
                    'title' => $menu_title,
                    'url' => $url,
                    'parent' => $parent,
                    'description' => $description,
                    'image' => $image,
                    'displayorder' => $displayorder,
                        ), array(
                    'id' => $id,
                ));
                $this->getSession()->setFlash('success', Language::$phrases['message']['update_success']);
                $this->redirect(DASHBOARD_URL . "/menu/?name=" . $menu['name']);
            }

            $this->render("menu/edit", array(
                'title' => $title,
                'formview' => $form->createView(),
            ));
        }
    }

    function delete($id) {
        // Check permission
        if ($this->current_user['capability']['menu']['manage'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $request = $this->getRequest();
        $menus = $this->model->getMenuByID($id);
        $url = DASHBOARD_URL . '/menu/?name=' . $request->get('name');
        if (count($menus) <= 0) {
            $this->redirect($url);
        } else {
            $menu = $menus[0];
            if ($this->model->delete($id)) {
                $this->getSession()->setFlash('success', Language::$phrases['message']['delete_success']);

                $this->model->update(array(
                    'parent' => 0,
                        ), array(
                    'parent' => $id,
                    'name' => $menu['name']
                ));
            } else {
                $this->getSession()->setFlash('warning', Language::$phrases['message']['error_occur']);
            }
            $this->redirect($url);
        }
    }

}
