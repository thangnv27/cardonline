<?php

class OptionAdmin extends AdminController {

    private $current_user;
    private $filename;

    function __construct() {
        parent::__construct();
        $this->current_user = UserAdmin::checkLogin();
        $this->filename = DB_PATH . 'options.json';
    }

    function index() {
        if ($this->current_user['capability']['options']['edit'] == 0) {
            Debug::throwException(Language::$phrases['message']['error_occur'], null);
        }

        $title = Language::$phrases['page']['option']['title.index'];
        $option = $this->get_option();
        $site_option = $option->site_option;
        $code_coin = $option->code_coin;

        $form = new Form($title);
        $form->add("name", "text", array(
                    'label' => Language::$phrases['page']['option']['name'],
                    'data' => $site_option->name,
                ))
                ->add('description', 'textarea', array(
                    'label' => Language::$phrases['page']['option']['description'],
                    'data' => $site_option->description,
                ))
                ->add("keywords", "text", array(
                    'label' => Language::$phrases['page']['option']['keywords'],
                    'data' => $site_option->keywords,
                ))
                ->add("logo", "upload", array(
                    'label' => "Logo",
                    'data' => $site_option->logo,
                    'btn' => array(
                        'onclick' => "openFileDialog('logo')"
                    )
                ))
                ->add("favicon", "upload", array(
                    'label' => "Favicon",
                    'data' => $site_option->favicon,
                    'btn' => array(
                        'onclick' => "openFileDialog('favicon')"
                    )
                ))
                ->add("sologan", "text", array(
                    'label' => "Sologan",
                    'data' => $site_option->sologan,
                ))
                ->add("admin_email", "text", array(
                    'label' => Language::$phrases['page']['option']['admin_email'],
                    'data' => $site_option->admin_email,
                ))
                ->add("ga_id", "text", array(
                    'label' => Language::$phrases['page']['option']['ga_id'],
                    'data' => $site_option->ga_id,
                    'description' => Language::$phrases['context']['example'] . ': UA-40210538-1 '
                ))
                ->add("youtube_link", "text", array(
                    'label' => 'Link youtube hướng dẫn',
                    'data' => $site_option->youtube_link,
                    'description' => 'VD: http://www.youtube.com/embed/90II8VabE4U'
                ))
                ->add("footer_info", "textarea", array(
                    'label' => 'Thông tin chuyển khoản',
                    'data' => stripslashes($site_option->footer_info),
                    'attr' => array(
                        'class' => 'editor'
                    )
                ))
                ->add("payment_rate", "textarea", array(
                    'label' => 'Bảng giá',
                    'data' => stripslashes($site_option->payment_rate),
                    'attr' => array(
                        'class' => 'editor'
                    )
                ))
                // payment information
                ->add("code1", "text", array(
                    'label' => 'Giá 1 code',
                    'data' => $code_coin->code1,
                ))
                ->add("code3", "text", array(
                    'label' => 'Giá 3 code',
                    'data' => $code_coin->code3,
                ))
                ->add("code7", "text", array(
                    'label' => 'Giá 7 code',
                    'data' => $code_coin->code7,
                ))
                ->add("code16", "text", array(
                    'label' => 'Giá 16 code',
                    'data' => $code_coin->code16,
                ))
                ->add("code24", "text", array(
                    'label' => 'Giá 24 code',
                    'data' => $code_coin->code24,
                ))
                ->add("code40", "text", array(
                    'label' => 'Giá 40 code',
                    'data' => $code_coin->code40,
        ));

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $name = $request->get('name');
            $description = $request->get('description');
            $keywords = $request->get('keywords');
            $logo = $request->get('logo');
            $favicon = $request->get('favicon');
            $sologan = $request->get('sologan');
            $admin_email = $request->get('admin_email');
            $ga_id = $request->get('ga_id');
            $youtube_link = $request->get('youtube_link');
            $footer_info = $request->get('footer_info');
            $payment_rate = $request->get('payment_rate');
            
            $code1 = $request->get('code1');
            $code3 = $request->get('code3');
            $code7 = $request->get('code7');
            $code16 = $request->get('code16');
            $code24 = $request->get('code24');
            $code40 = $request->get('code40');
            
            $data = json_encode(array(
                'site_option' => array(
                    'name' => $name,
                    'description' => $description,
                    'keywords' => $keywords,
                    'logo' => $logo,
                    'favicon' => $favicon,
                    'sologan' => $sologan,
                    'admin_email' => $admin_email,
                    'ga_id' => $ga_id,
                    'youtube_link' => $youtube_link,
                    'footer_info' => $footer_info,
                    'payment_rate' => $payment_rate,
                ),
                'code_coin' => array(
                    'code1' => $code1,
                    'code3' => $code3,
                    'code7' => $code7,
                    'code16' => $code16,
                    'code24' => $code24,
                    'code40' => $code40,
                )
            ));
            write_file($this->filename, $data);
            $this->redirect(DASHBOARD_URL . '/option/');
        }

        $this->render("option", array(
            'title' => $title,
            'form' => $form,
        ));
    }

    function get_option() {
        $option = file_get_contents($this->filename);
        return json_decode($option);
    }

}
