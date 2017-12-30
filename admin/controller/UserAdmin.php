<?php

class UserAdmin extends AdminController {

    private $current_user;
    private static $user_login;

    function __construct() {
        parent::__construct();
        $this->current_user = self::checkLogin();
    }

    public static function checkLogin() {
        if (isset($_SESSION['user_logged_in'])) {
            self::$user_login = $_SESSION['user_logged_in'];
        } else {
            $array = array(
                get_admin_login_url(),
                DASHBOARD_URL . '/user/forgot_password/',
                DASHBOARD_URL . '/user/checkuserloggedin/',
            );
            $current_url = trailingslashit(getCurrentRquestUrl());
            if (!in_array($current_url, $array)) {
                header("location:" . get_admin_login_url());
                exit();
            }
        }
        return self::$user_login;
    }

    function index() {
        if ($this->current_user['capability']['users']['view'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        ## Bulk Actions
        $request = $this->getRequest();
        $action = $request->get('action');
        $url = DASHBOARD_URL . '/user/';
        switch ($action) {
            case 'delete':
                // Check permission
                if ($this->current_user['capability']['users']['delete'] == 0) {
                    Debug::throwException(Language::$phrases['message']['error_occur'], null);
                }

                $checked = $request->get('item');
                if (count($checked) > 0) {
                    foreach ($checked as $key => $id) {
                        if ($id == $this->current_user['id'] or $id == 1) {
                            unset($checked[$key]);
                        }
                    }
                    if (count($checked) > 0) {
                        $user_ID = implode(", ", $checked);
                        $this->model->delete("id IN ($user_ID)");
                        $this->getSession()->setFlash('success', Language::$phrases['message']['delete_success']);
                    }
                }
                $this->redirect($url);
                break;
            case "activate":
                // Check permission
                if ($this->current_user['capability']['users']['edit'] == 0) {
                    Debug::throwException(Language::$phrases['message']['error_occur'], null);
                }

                $checked = $request->get('item');
                if (count($checked) > 0) {
                    $user_ID = implode(", ", $checked);
                    $this->model->activateUser("id IN ($user_ID) AND activation_key<>''");
                    $this->getSession()->setFlash('success', Language::$phrases['message']['activate_success']);
                }
                $this->redirect($url);
            default:
                break;
        }

        $title = Language::$phrases['page']['user']['title.index'];

        ## List table
        $table = new Table($title);
        $columns = array(
            'col_cbox' => '<input type="checkbox" id="checkall" />',
            'col_id' => 'ID',
            'col_username' => Language::$phrases['page']['user']['username'],
            'col_email' => Language::$phrases['page']['user']['email'],
            'col_referer' => Language::$phrases['page']['user']['referer'],
            'col_group' => Language::$phrases['page']['user']['group'],
            'col_registered_date' => Language::$phrases['page']['user']['registered_date'],
            'col_options' => Language::$phrases['context']['options'],
        );
        $row = "";
        $table->add_columns($columns);

        $whereCount = "";
        $whereAll = "";
        $usergroup = $request->get('group');
        $search_query = $request->get('s');
        if (!empty($usergroup)) {
            $whereCount = "role = '$usergroup'";
            $whereAll = "U.role = '$usergroup'";
        }
        if (!empty($search_query)) {
            if (!empty($usergroup)) {
                $whereCount .= " AND (username LIKE '%$search_query%' OR email LIKE '%$search_query%' OR ip_address LIKE '%$search_query%')";
                $whereAll .= " AND (U.username LIKE '%$search_query%' OR U.email LIKE '%$search_query%' OR U.ip_address LIKE '%$search_query%')";
            } else {
                $whereCount = "(username LIKE '%$search_query%' OR email LIKE '%$search_query%' OR ip_address LIKE '%$search_query%')";
                $whereAll = "(U.username LIKE '%$search_query%' OR U.email LIKE '%$search_query%' OR U.ip_address LIKE '%$search_query%')";
            }
        }

        // Pagination
        $currentURL = trailingslashit($request->getCurrentRquestUrl());
        if (count($request->all()) > 0) {
            $currentURL = $request->getCurrentRquestUrl();
        }
        $limit = 50;
        $pager = new Pagenavi($currentURL, $request->get('page'), $limit);
        $start = $pager->start($limit);
        $countRecords = $this->model->countUsers($whereCount);
        $table->add_pagenavi($pager->pageList($countRecords));
        $table->caption = $title . ": " . $countRecords;

        //Get the records registered in the prepare_items method
        $records = $this->model->getUsers($start, $limit, $whereAll);

        //Loop for each record
        if (is_array($records) and ! empty($records)) {
            foreach ($records as $rec) {
                //Open the line
                $row .= '<tr id="row_' . $rec->ID . '">';
                foreach ($columns as $field => $col_name) {
                    $class = "class='$field column-$field' ";
                    $style = "";
                    $attributes = $class . $style;

                    $permission_link = DASHBOARD_URL . '/user/' . $rec['id'] . '/permission';
                    $edit_link = DASHBOARD_URL . '/user/' . $rec['id'] . '/edit';
                    $delete_link = DASHBOARD_URL . '/user/' . $rec['id'] . '/delete';
                    $activate_link = DASHBOARD_URL . '/user/' . $rec['id'] . '/activation';

                    //Display the cell
                    switch ($field) {
                        case "col_cbox":
                            $row .= '<td ' . $attributes . '>';
                            if ($rec['id'] != $this->current_user['id'] and $rec['id'] != 1) {
                                $row .= '<input type="checkbox" name="item[]" value="' . $rec['id'] . '" />';
                            }
                            $row .= '</td>';
                            break;
                        case "col_id":
                            $row .= '<td ' . $attributes . '>' . $rec['id'] . '</td>';
                            break;
                        case "col_username":
                            $row .= '<td ' . $attributes . '><a href="' . $edit_link . '">' . $rec['username'] . '</a></td>';
                            break;
                        case "col_email":
                            $row .= '<td ' . $attributes . '><a href="mailto:' . $rec['email'] . '">' . $rec['email'] . '</a></td>';
                            break;
                        case "col_referer":
                            $row .= '<td ' . $attributes . '>' . $rec['user_referer'] . '</td>';
                            break;
                        case "col_group":
                            $row .= '<td ' . $attributes . '>' . $rec['groupname'] . '</td>';
                            break;
                        case "col_registered_date":
                            $row .= '<td ' . $attributes . '>' . $rec['registered_date'] . '</td>';
                            break;
                        case "col_options":
                            $row .= '<td ' . $attributes . '>';
                            if ($rec['id'] != $this->current_user['id']) {
                                if ($this->current_user['capability']['users']['permission'] == 1 and $rec['id'] != 1)
                                    $row .= '<a href="' . $permission_link . '" class="btn btn-info btn-xs">' . Language::$phrases['action']['permission'] . '</a> ';
                                if ($this->current_user['capability']['users']['edit'] == 1 and $rec['id'] != 1)
                                    $row .= '<a href="' . $edit_link . '" class="btn btn-primary btn-xs">' . Language::$phrases['action']['edit'] . '</a> ';
                                if ($this->current_user['capability']['users']['delete'] == 1 and $rec['id'] != 1)
                                    $row .= '<a href="' . $delete_link . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . Language::$phrases['action']['delete.confirm'] . '\');">' . Language::$phrases['action']['delete'] . '</a> ';
                                if ($this->current_user['capability']['users']['edit'] == 1 and $rec['id'] != 1 and $rec['activation_key'] != "")
                                    $row .= '<a href="' . $activate_link . '" class="btn btn-warning btn-xs" onclick="return confirm(\'' . Language::$phrases['action']['activate.confirm'] . '\');">' . Language::$phrases['action']['activate'] . '</a> ';
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

        $usergroupModel = new UserGroupAdminModel();

        $this->render("user/index", array(
            'title' => $title,
            'filter_group' => $usergroupModel->getUserGroups(),
            'table' => $table->createView(),
            'request' => $request,
        ));
    }

    function activation($user_id) {
        $user = $this->model->getUserByFieldUnique($user_id);
        $url = DASHBOARD_URL . "/user/";
        if (!$user) {
            $this->redirect($url);
        } elseif ($user_id == $this->current_user['id']) {
            $this->redirect($url);
        } elseif ($this->current_user['capability']['users']['edit'] == 0 or $user_id == 1) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        } else {
            $this->model->activateUser(array('id' => $user_id));
            $this->getSession()->setFlash('success', Language::$phrases['message']['activate_success']);
        }
        $this->redirect($url);
    }

    function addnew() {
        if ($this->current_user['capability']['users']['create'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $title = Language::$phrases['page']['user']['title.addnew'];

        $request = $this->getRequest();
        $username = $request->get('username');
        $password = $request->get('password');
        $email = $request->get('email');
        $referer = $request->get('referer');
        $role = $request->get('role');
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $gender = $request->get('gender');
        $dob = $request->get('dob');
        $phone = $request->get('phone');
        $coin = $request->get('coin');
        $website = $request->get('website');
        $yahoo = $request->get('yahoo');
        $skype = $request->get('skype');
        $about = $request->get('about');
        $confirm = $request->get('confirm');

        $roles = $this->model->getRoles();

        $form = new Form($title, array(
            'action' => '',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));
        $form->add('username', 'text', array(
                    'label' => Language::$phrases['page']['user']['username'] . "*",
                    'description' => 'Use to login.',
                    'data' => $username,
                ))
                ->add('password', 'password', array(
                    'label' => Language::$phrases['page']['user']['password'] . "*",
                    'description' => 'Use to login for Username.',
                    'data' => $password,
                ))
                ->add('email', 'email', array(
                    'label' => Language::$phrases['page']['user']['email'] . "*",
                    'data' => $email,
                    'attr' => array(
                        'placeholder' => 'example@domain.com'
                    )
                ))
                ->add('referer', 'text', array(
                    'label' => Language::$phrases['page']['user']['referer'],
                    'description' => 'User referral',
                    'data' => $referer,
                ))
                ->add('role', 'choice', array(
                    'label' => Language::$phrases['page']['user']['group'] . "*",
                    'choices' => $roles,
                    'data' => $role,
                ))
                ->add('first_name', 'text', array(
                    'label' => Language::$phrases['page']['user']['firstname'],
                    'data' => $first_name,
                ))
                ->add('last_name', 'text', array(
                    'label' => Language::$phrases['page']['user']['lastname'],
                    'data' => $last_name,
                ))
                ->add('gender', 'choice', array(
                    'label' => Language::$phrases['page']['user']['gender'],
                    'choices' => Utils::gender(),
                    'data' => $gender,
                ))
                ->add('dob', 'text', array(
                    'label' => Language::$phrases['page']['user']['dob'],
                    'data' => $dob,
                    'attr' => array(
                        'placeholder' => 'yyyy-mm-dd'
                    )
                ))
                ->add('phone', 'text', array(
                    'label' => Language::$phrases['page']['user']['phone'],
                    'data' => $phone,
                ))
                ->add('coin', 'text', array(
                    'label' => Language::$phrases['page']['user']['coin'],
                    'data' => $coin,
                ))
                ->add('website', 'text', array(
                    'label' => Language::$phrases['page']['user']['website'],
                    'data' => $website,
                ))
                ->add('yahoo', 'text', array(
                    'label' => Language::$phrases['page']['user']['yahoo'],
                    'data' => $yahoo,
                ))
                ->add('skype', 'text', array(
                    'label' => Language::$phrases['page']['user']['skype'],
                    'data' => $skype,
                ))
                ->add('about', 'textarea', array(
                    'label' => Language::$phrases['page']['user']['about'],
                    'data' => $about,
                    'attr' => array(
                        'rows' => 5
                    )
        ));
        if ($confirm == 'on') {
            $form->add('confirm', 'checkbox', array(
                'label' => Language::$phrases['page']['user']['require_confirm'],
                'description' => Language::$phrases['page']['user']['require_confirm.description'],
                'attr' => array(
                    'class' => 'cbswitch',
                    'checked' => 'checked',
                )
            ));
        } else {
            $form->add('confirm', 'checkbox', array(
                'label' => Language::$phrases['page']['user']['require_confirm'],
                'description' => Language::$phrases['page']['user']['require_confirm.description'],
                'attr' => array(
                    'class' => 'cbswitch',
                )
            ));
        }

        if ($request->getMethod() == 'POST') {
            $msg = "";
            if (!Utils::is_valid_username($username)) {
                $msg .= "<p>" . Language::$phrases['message']['username.invalid'] . "</p>";
            } elseif ($this->model->isUsernameExists($username)) {
                $msg .= "<p>" . Language::$phrases['message']['username.exists'] . "</p>";
            }
            if ($password == "") {
                $msg .= "<p>" . Language::$phrases['message']['empty_password'] . "</p>";
            }
            if (!Utils::is_valid_email($email)) {
                $msg .= "<p>" . Language::$phrases['message']['email.invalid'] . "</p>";
            } elseif ($this->model->isEmailExists($email)) {
                $msg .= "<p>" . Language::$phrases['message']['email.exists'] . "</p>";
            }
            if ($referer != "" and ! $this->model->isUsernameExists($referer)) {
                $msg .= "<p>" . Language::$phrases['message']['referer_not_exists'] . "</p>";
            }
            if (!array_key_exists($role, $roles)) {
                $msg .= "<p>" . Language::$phrases['message']['role_not_exists'] . "</p>";
            }
            if (!array_key_exists($gender, Utils::gender())) {
                $msg .= "<p>" . Language::$phrases['message']['gender.invalid'] . "</p>";
            }
            if ($dob != "") {
                if (preg_match("#^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$#si", $dob)) {
                    $dob1 = explode("-", $dob);
                    if (!in_array($dob1[0], range(1945, ((int) date('Y') - 6))) or ! in_array($dob1[1], range(1, 12)) or ! in_array($dob1[2], range(1, 31))) {
                        $msg .= "<p>" . Language::$phrases['message']['dob.invalid'] . "</p>";
                    }
                } else {
                    $msg .= "<p>" . Language::$phrases['message']['dob.invalid'] . "</p>";
                }
            }
            if ($phone != "" and ! Utils::is_valid_phone_number($phone)) {
                $msg .= "<p>" . Language::$phrases['message']['phone.invalid'] . "</p>";
            }
            if ($msg != "") {
                $this->getSession()->setFlash('warning', $msg);
            } else {
                $salt = Utils::fetch_user_salt(30);
                $pwd = Utils::hash_password($password, $salt);
                $capability = $this->model->getCapabilityByRole($role);
                $activation_key = "";
                $basic = array(
                    'user_referer' => $referer,
                    'username' => $username,
                    'email' => $email,
                    'password' => $pwd,
                    'salt' => $salt,
                    'role' => $role,
                    'capability' => $capability,
                );
                if ($confirm == "on") {
                    $activation_key = ""; // generate activation key
                    $basic['activation_key'] = $activation_key;
                }
                $user_id = $this->model->createUser($basic);
                if ($user_id) {
                    $meta = array(
                        'user_id' => $user_id,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'gender' => $gender,
                        'phone' => $phone,
                        'coin' => $coin,
                        'website' => $website,
                        'yahoo' => $yahoo,
                        'skype' => $skype,
                        'about' => $about,
                    );
                    if (!empty($dob)) {
                        $meta['dob'] = $dob;
                    }
                    $this->model->createUserMeta($meta);
                    $this->getSession()->setFlash('success', Language::$phrases['message']['create_success']);

                    // Send mail
                    $optionAdmin = new OptionAdmin();
                    $option = $optionAdmin->get_option();
                    $site_option = $option->site_option;
                    $template = $this->getTemplate();
                    $mailer = new Mailer();
                    $subject = $template->assignValue(Language::$phrases['message']['mail.create_user.subject'], array(
                        'name' => ($first_name != "") ? $first_name : $username,
                    ));
                    $body = $template->assignValue(Language::$phrases['message']['mail.create_user.body'], array(
                        'name' => ($first_name != "") ? $first_name : $username,
                        'username' => $username,
                        'password' => $password,
                        'sitename' => $site_option->name,
                        'login_url' => get_user_login_url(),
                    ));
                    $message = $mailer->createMessage($subject, $body, "text/html", "UTF-8");
                    $message->setTo($email)
                            ->setFrom($site_option->name);
                    $mailer->createMailer()->send($message);

                    if ($confirm == "on") {
                        // send mail activate
                    }

                    $url = DASHBOARD_URL . '/user/' . $user_id . '/edit';
                    $this->redirect($url);
                }
            }
        }

        $this->render("user/new", array(
            'title' => $title,
            'formview' => $form->createView(),
        ));
    }

    function edit($user_id) {
        $user = $this->model->getUserByFieldUnique($user_id);
        $url = DASHBOARD_URL . "/user/";
        if (!$user) {
            $this->redirect($url);
        } elseif ($user_id == $this->current_user['id']) {
            $this->redirect($url);
        } elseif ($this->current_user['capability']['users']['edit'] == 0 or $user_id == 1) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $roles = $this->model->getRoles();
        $title = Language::$phrases['page']['user']['title.edit'];
        $form = new Form($title, array(
            'action' => '',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));
        $form->add('username', 'text', array(
                    'label' => Language::$phrases['page']['user']['username'] . "*",
                    'description' => 'Use to login.',
                    'data' => $user['username'],
                ))
                ->add('password', 'password', array(
                    'label' => Language::$phrases['page']['user']['password'] . "*",
                    'description' => 'Use to login for Username.',
                ))
                ->add('email', 'email', array(
                    'label' => Language::$phrases['page']['user']['email'] . "*",
                    'data' => $user['email'],
                    'attr' => array(
                        'placeholder' => 'example@domain.com'
                    )
                ))
                ->add('referer', 'text', array(
                    'label' => Language::$phrases['page']['user']['referer'],
                    'description' => 'User referral',
                    'data' => $user['referer'],
                ))
                ->add('role', 'choice', array(
                    'label' => Language::$phrases['page']['user']['group'] . "*",
                    'choices' => $roles,
                    'data' => $user['role'],
                ))
                ->add('first_name', 'text', array(
                    'label' => Language::$phrases['page']['user']['firstname'],
                    'data' => $user['first_name'],
                ))
                ->add('last_name', 'text', array(
                    'label' => Language::$phrases['page']['user']['lastname'],
                    'data' => $user['last_name'],
                ))
                ->add('gender', 'choice', array(
                    'label' => Language::$phrases['page']['user']['gender'],
                    'choices' => Utils::gender(),
                    'data' => $user['gender'],
                ))
                ->add('dob', 'text', array(
                    'label' => Language::$phrases['page']['user']['dob'],
                    'data' => $user['dob'],
                    'attr' => array(
                        'placeholder' => 'yyyy-mm-dd'
                    )
                ))
                ->add('phone', 'text', array(
                    'label' => Language::$phrases['page']['user']['phone'],
                    'data' => $user['phone'],
                ))
                ->add('coin', 'text', array(
                    'label' => Language::$phrases['page']['user']['coin'],
                    'data' => $user['coin'],
                ))
                ->add('website', 'text', array(
                    'label' => Language::$phrases['page']['user']['website'],
                    'data' => $user['website'],
                ))
                ->add('yahoo', 'text', array(
                    'label' => Language::$phrases['page']['user']['yahoo'],
                    'data' => $user['yahoo'],
                ))
                ->add('skype', 'text', array(
                    'label' => Language::$phrases['page']['user']['skype'],
                    'data' => $user['skype'],
                ))
                ->add('about', 'textarea', array(
                    'label' => Language::$phrases['page']['user']['about'],
                    'data' => $user['about'],
                    'attr' => array(
                        'rows' => 5
                    )
                ))
                ->add('confirm', 'checkbox', array(
                    'label' => Language::$phrases['page']['user']['require_confirm'],
                    'description' => Language::$phrases['page']['user']['require_confirm.description'],
                    'attr' => array(
                        'class' => 'cbswitch',
                    )
                ))
                ->add('ip_address', 'text', array(
                    'label' => "IP Address",
                    'data' => $user['ip_address'],
        ));

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $username = $request->get('username');
            $password = $request->get('password');
            $email = $request->get('email');
            $referer = $request->get('referer');
            $role = $request->get('role');
            $first_name = $request->get('first_name');
            $last_name = $request->get('last_name');
            $gender = $request->get('gender');
            $dob = $request->get('dob');
            $phone = $request->get('phone');
            $coin = $request->get('coin');
            $website = $request->get('website');
            $yahoo = $request->get('yahoo');
            $skype = $request->get('skype');
            $about = $request->get('about');
            $confirm = $request->get('confirm');
            $ip_address = $request->get('ip_address');
            $msg = "";
            if (!Utils::is_valid_username($username)) {
                $msg .= "<p>" . Language::$phrases['message']['username.invalid'] . "</p>";
            } elseif ($username != $user['username'] and $this->model->isUsernameExists($username)) {
                $msg .= "<p>" . Language::$phrases['message']['username.exists'] . "</p>";
            }
            if (!Utils::is_valid_email($email)) {
                $msg .= "<p>" . Language::$phrases['message']['email.invalid'] . "</p>";
            } elseif ($email != $user['email'] and $this->model->isEmailExists($email)) {
                $msg .= "<p>" . Language::$phrases['message']['email.exists'] . "</p>";
            }
            if ($referer != "" and ! $this->model->isUsernameExists($referer)) {
                $msg .= "<p>" . Language::$phrases['message']['referer_not_exists'] . "</p>";
            }
            if (!array_key_exists($role, $roles)) {
                $msg .= "<p>" . Language::$phrases['message']['role_not_exists'] . "</p>";
            }
            if (!array_key_exists($gender, Utils::gender())) {
                $msg .= "<p>" . Language::$phrases['message']['gender.invalid'] . "</p>";
            }
            if ($dob != "") {
                if (preg_match("#^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$#si", $dob)) {
                    $dob1 = explode("-", $dob);
                    if (!in_array($dob1[0], range(1945, ((int) date('Y') - 6))) or ! in_array($dob1[1], range(1, 12)) or ! in_array($dob1[2], range(1, 31))) {
                        $msg .= "<p>" . Language::$phrases['message']['dob.invalid'] . "</p>";
                    }
                } else {
                    $msg .= "<p>" . Language::$phrases['message']['dob.invalid'] . "</p>";
                }
            }
            if ($phone != "" and ! Utils::is_valid_phone_number($phone)) {
                $msg .= "<p>" . Language::$phrases['message']['phone.invalid'] . "</p>";
            }
            if ($msg != "") {
                $this->getSession()->setFlash('warning', $msg);
            } else {
                $activation_key = "";
                $basic = array(
                    'user_referer' => $referer,
                    'username' => $username,
                    'email' => $email,
                    'role' => $role,
                    'ip_address' => $ip_address,
                );
                if ($password != "") {
                    $pwd = Utils::hash_password($password, $user['salt']);
                    $basic['password'] = $pwd;
                }
                if ($confirm == "on") {
                    $activation_key = ""; // generate activation key
                    $basic['activation_key'] = $activation_key;
                }
                if ($user['role'] != $role) {
                    $capability = $this->model->getCapabilityByRole($role);
                    $basic['capability'] = $capability;
                }
                $meta = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'gender' => $gender,
                    'phone' => $phone,
                    'coin' => $coin,
                    'website' => $website,
                    'yahoo' => $yahoo,
                    'skype' => $skype,
                    'about' => $about,
                );
                if (!empty($dob)) {
                    $meta['dob'] = $dob;
                }
                $this->model->updateUser($basic, $meta, $user_id);
                $this->getSession()->setFlash('success', Language::$phrases['message']['update_success']);

                // Send mail update account
                /* some code */

                if ($confirm == "on") {
                    // send mail activate
                }

                $this->redirect($request->getCurrentRquestUrl());
            }
        }

        $this->render("user/edit", array(
            'title' => $title,
            'formview' => $form->createView(),
            'user' => $user,
        ));
    }

    function delete($user_id) {
        $user = $this->model->getUserByFieldUnique($user_id);
        $url = DASHBOARD_URL . "/user/";
        if (!$user) {
            $this->redirect($url);
        } elseif ($user_id == $this->current_user['id']) {
            $this->redirect($url);
        } elseif ($this->current_user['capability']['users']['delete'] == 0 or $user_id == 1) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        } else {
            $this->model->delete(array('id' => $user_id));
            $this->getSession()->setFlash('success', Language::$phrases['message']['delete_success']);
        }
        $this->redirect($url);
    }

    function permission($user_id) {
        $user = $this->model->getUserByFieldUnique($user_id);
        $url = DASHBOARD_URL . "/user/";
        if (!$user) {
            $this->redirect($url);
        } elseif ($user_id == $this->current_user['id']) {
            $this->redirect($url);
        } elseif ($this->current_user['capability']['users']['permission'] == 0 or $user_id == 1) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $capability = $request->get('capability');
            $userCapability = $user['capability'];
            foreach ($userCapability as $fn => $act) {
                foreach ($act as $k => $v) {
                    if (!$capability or ! array_key_exists($fn, $capability) or ! array_key_exists($k, $capability[$fn])) {
                        $userCapability[$fn][$k] = 0;
                    } else {
                        $userCapability[$fn][$k] = 1;
                    }
                }
            }
            $this->model->updateCapability($userCapability, $user_id);
            $this->redirect($request->getCurrentRquestUrl());
        }

        $this->render("user/permission", array(
            'title' => Language::$phrases['page']['user']['title.permission'],
            'user' => $user,
        ));
    }

    function login() {
        if (isset($_SESSION['user_logged_in'])) {
            $this->redirect(DASHBOARD_URL);
        }
        $request = $this->getRequest();
        $response = $this->response();
        if ($request->getMethod() == 'POST') {
            $username = $request->get('username');
            $password = $request->get('password');
            $lang_code = $request->get('lang');
            $redirect_url = $request->get('redirect_url');
            $msg = "";

            if (!Utils::is_valid_username($username)) {
                $msg .= "<p>" . Language::$phrases['message']['username.invalid'] . "</p>";
            }
            if ($password == "") {
                $msg .= "<p>" . Language::$phrases['message']['empty_password'] . "</p>";
            }
            if ($msg == "") {
                $salt = $this->model->getSalt($username);
                $hash_password = Utils::hash_password($password, $salt);
                $user = $this->model->getUserLogin(array(
                    'username' => $username,
                    'password' => $hash_password,
                    'is_deleted' => 0,
                ));
                if (count($user) != 1) {
                    $msg .= "<p>" . Language::$phrases['message']['user_incorrect'] . "</p>";
                } else {
                    if ($user[0]['activation_key'] != "") {
                        $msg .= "<p>" . Language::$phrases['message']['user_not_activate'] . "</p>";
                    } else {
                        unset($user[0]['password']);
                        $user[0]['user_id'] = $user[0]['id'];
                        $user[0]['capability'] = @unserialize($user[0]['capability']);
                        $user[0]['ip_logged_in'] = $_SERVER['REMOTE_ADDR'];
                        $_SESSION['user_logged_in'] = $user[0];
                    }
                }
            }
            if ($msg != "") {
                $response->setContent(json_encode(array(
                    'status' => 'error',
                    'message' => $msg,
                )));
            } else {
                $redirect_url = ($redirect_url != "") ? $redirect_url : DASHBOARD_URL;
                $response->setContent(json_encode(array(
                    'status' => 'success',
                    'redirect_url' => $redirect_url,
                )));
            }
            $response->sendContent();

            // Set default language
            if (file_exists(LANG_PATH . $lang_code . '.xml') or file_exists(LANG_PATH . "admin." . $lang_code . '.xml')) {
                $lang = new Language();
                $lang->setLang($lang_code);
                Language::$lang_code = $lang_code;
            }
        } else {
            $this->render("login", array(
                'title' => Language::$phrases['page']['user']['title.login'],
            ));
        }
    }

    function forgot_password() {
        $request = $this->getRequest();
        $response = $this->response();
        if ($request->getMethod() == 'POST') {
            $email = $request->get('email');
            $msg = "";

            if (!Utils::is_valid_email($email)) {
                $msg .= "<p>" . Language::$phrases['message']['email.invalid'] . "</p>";
            } elseif (!$this->model->isEmailExists($email)) {
                $msg .= "<p>" . Language::$phrases['message']['email.not_exists'] . "</p>";
            }
            if ($msg == "") {
                $user = $this->model->getUserByFieldUnique($email);
                $username = $user['username'];
                $first_name = $user['first_name'];
                $salt = $user['salt'];
                $password = Utils::fetch_random_password();
                $hash_password = Utils::hash_password($password, $salt);
                $result = $this->model->updatePassword($hash_password, $user['id']);
                if ($result) {
                    $optionAdmin = new OptionAdmin();
                    $option = $optionAdmin->get_option();
                    $site_option = $option->site_option;
                    $template = $this->getTemplate();
                    $mailer = new Mailer();
                    $subject = $template->assignValue(Language::$phrases['message']['mail.forgotpassword.subject'], array(
                        'name' => ($first_name != "") ? $first_name : $username,
                    ));
                    $body = $template->assignValue(Language::$phrases['message']['mail.forgotpassword.body'], array(
                        'name' => ($first_name != "") ? $first_name : $username,
                        'username' => $username,
                        'password' => $password,
                        'sitename' => $site_option->name,
                        'login_url' => get_user_login_url(),
                    ));
                    $message = $mailer->createMessage($subject, $body, "text/html", "UTF-8");
                    $message->setTo($email)
                            ->setFrom($site_option->name);
                    $mailer->createMailer()->send($message);
                    $response->setContent(json_encode(array(
                        'status' => 'success',
                        'message' => Language::$phrases['message']['mail_send_success'],
                    )));
                } else {
                    $response->setContent(json_encode(array(
                        'status' => 'error',
                        'message' => Language::$phrases['message']['error_occur'],
                    )));
                }
            } elseif ($msg != "") {
                $response->setContent(json_encode(array(
                    'status' => 'error',
                    'message' => $msg,
                )));
            }
        } else {
            $response->setContent(json_encode(array(
                'status' => 'error',
                'message' => Language::$phrases['message']['error_occur'],
            )));
        }
        $response->sendContent();
    }

    function logout() {
        $role = $this->current_user['role'];
        unset($_SESSION['user_logged_in']);

        if ($role != 'subscriber') {
            $this->redirect(get_admin_login_url());
        } else {
            $this->redirect(get_user_login_url());
        }
    }

    function checkuserloggedin() {
        $request = $this->getRequest();
        $response = $this->response();
        if ($request->getMethod() == 'POST' and $request->get('check') == 1) {
            if (isset($_SESSION['user_logged_in'])) {
                $response->setContent(json_encode(array(
                    'status' => 'success',
                )));
            } else {
                $response->setContent(json_encode(array(
                    'status' => 'error',
                )));
            }
        } else {
            $response->setContent(json_encode(array(
                'status' => 'none',
            )));
        }
        $response->sendContent();
    }

    function profile() {
        $user_id = $this->current_user['id'];
        $user = $this->model->getUserByFieldUnique($user_id);
        if (!$user) {
            $this->redirect(DASHBOARD_URL);
        }

        $title = Language::$phrases['page']['user']['title.edit.profile'];
        $form = new Form($title, array(
            'action' => '',
            'method' => 'post',
            'class' => 'form-horizontal'
        ));
        $form->add('username', 'static', array(
                    'label' => Language::$phrases['page']['user']['username'],
                    'data' => $user['username'],
                ))
                ->add('email', 'static', array(
                    'label' => Language::$phrases['page']['user']['email'],
                    'data' => $user['email'],
                ))
                ->add('role', 'static', array(
                    'label' => Language::$phrases['page']['user']['group'],
                    'data' => $this->model->getRoleName($user['role']),
                ))
                ->add('referer', 'static', array(
                    'label' => Language::$phrases['page']['user']['referer'],
                    'data' => $user['referer'],
                ))
                ->add('password', 'password', array(
                    'label' => Language::$phrases['page']['user']['password'],
                ))
                ->add('confirm_password', 'password', array(
                    'label' => Language::$phrases['page']['user']['confirm_password'],
                ))
                ->add('first_name', 'text', array(
                    'label' => Language::$phrases['page']['user']['firstname'],
                    'data' => $user['first_name'],
                ))
                ->add('last_name', 'text', array(
                    'label' => Language::$phrases['page']['user']['lastname'],
                    'data' => $user['last_name'],
                ))
                ->add('gender', 'choice', array(
                    'label' => Language::$phrases['page']['user']['gender'],
                    'choices' => Utils::gender(),
                    'data' => $user['gender'],
                ))
                ->add('dob', 'text', array(
                    'label' => Language::$phrases['page']['user']['dob'],
                    'data' => $user['dob'],
                    'description' => 'yyyy-mm-dd',
                    'attr' => array(
                        'placeholder' => 'yyyy-mm-dd'
                    )
                ))
                ->add('phone', 'text', array(
                    'label' => Language::$phrases['page']['user']['phone'],
                    'data' => $user['phone'],
                ))
                ->add('website', 'text', array(
                    'label' => Language::$phrases['page']['user']['website'],
                    'data' => $user['website'],
                ))
                ->add('yahoo', 'text', array(
                    'label' => Language::$phrases['page']['user']['yahoo'],
                    'data' => $user['yahoo'],
                ))
                ->add('skype', 'text', array(
                    'label' => Language::$phrases['page']['user']['skype'],
                    'data' => $user['skype'],
                ))
                ->add('about', 'textarea', array(
                    'label' => Language::$phrases['page']['user']['about'],
                    'data' => $user['about'],
                    'attr' => array(
                        'rows' => 5
                    )
                ))
                ->add('ip_address', 'static', array(
                    'label' => "IP Address",
                    'data' => $user['ip_address'],
        ));

        if ($this->current_user['role'] === 'administrator') {
            $form->add('coin', 'text', array(
                'label' => Language::$phrases['page']['user']['coin'],
                'data' => $user['coin'],
            ));
        } else {
            $form->add('coin', 'text', array(
                'label' => Language::$phrases['page']['user']['coin'],
                'data' => $user['coin'],
                'attr' => array(
                    'disabled' => 'disabled'
                )
            ));
        }

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $password = $request->get('password');
            $confirm_password = $request->get('confirm_password');
            $first_name = $request->get('first_name');
            $last_name = $request->get('last_name');
            $gender = $request->get('gender');
            $dob = $request->get('dob');
            $phone = $request->get('phone');
            $coin = $request->get('coin');
            $website = $request->get('website');
            $yahoo = $request->get('yahoo');
            $skype = $request->get('skype');
            $about = $request->get('about');
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $msg = "";
            if ($password != "" and $confirm_password != $password) {
                $msg .= "<p>" . Language::$phrases['message']['conform_password_incorrect'] . "</p>";
            }
            if (!array_key_exists($gender, Utils::gender())) {
                $msg .= "<p>" . Language::$phrases['message']['gender.invalid'] . "</p>";
            }
            if ($dob != "") {
                if (preg_match("#^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$#si", $dob)) {
                    $dob1 = explode("-", $dob);
                    if (!in_array($dob1[0], range(1945, ((int) date('Y') - 6))) or ! in_array($dob1[1], range(1, 12)) or ! in_array($dob1[2], range(1, 31))) {
                        $msg .= "<p>" . Language::$phrases['message']['dob.invalid'] . "</p>";
                    }
                } else {
                    $msg .= "<p>" . Language::$phrases['message']['dob.invalid'] . "</p>";
                }
            }
            if ($phone != "" and ! Utils::is_valid_phone_number($phone)) {
                $msg .= "<p>" . Language::$phrases['message']['phone.invalid'] . "</p>";
            }
            if ($msg != "") {
                $this->getSession()->setFlash('warning', $msg);
            } else {
                $basic = array(
                    'ip_address' => $ip_address,
                );
                if ($password != "") {
                    $pwd = Utils::hash_password($password, $user['salt']);
                    $basic['password'] = $pwd;
                }
                $meta = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'gender' => $gender,
                    'dob' => $dob,
                    'phone' => $phone,
                    'website' => $website,
                    'yahoo' => $yahoo,
                    'skype' => $skype,
                    'about' => $about,
                );
                if ($this->current_user['role'] === 'administrator') {
                    $meta['coin'] = $coin;
                }
                $this->model->updateUser($basic, $meta, $user_id);
                $this->getSession()->setFlash('success', Language::$phrases['message']['update_success']);

                // Update current user logged in
                $user_update = $_SESSION['user_logged_in'];
                $user_update['ip_address'] = $ip_address;
                $user_update['first_name'] = $first_name;
                $user_update['last_name'] = $last_name;
                $user_update['gender'] = $gender;
                $user_update['dob'] = $dob;
                $user_update['phone'] = $phone;
                if ($this->current_user['role'] === 'administrator') {
                    $user_update['coin'] = $coin;
                }
                $user_update['website'] = $website;
                $user_update['yahoo'] = $yahoo;
                $user_update['skype'] = $skype;
                $user_update['about'] = $about;
                $_SESSION['user_logged_in'] = $user_update;

                $this->redirect($request->getCurrentRquestUrl());
            }
        }

        $this->render("user/profile", array(
            'title' => $title,
            'formview' => $form->createView(),
            'user' => $user,
        ));
    }

}
