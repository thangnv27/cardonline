<?php

class User extends Controller {

    function __construct() {
        parent::__construct();
    }

    function login() {
        if (isset($_SESSION['user_logged_in'])) {
            $this->redirect(Registry::$siteurl);
        }
        $this->render('login.tpl', array(
            'title' => "Đăng ký - Đăng nhập",
        ));
    }

    function register_process() {
        if (isset($_SESSION['user_logged_in']))
            die();

        $request = $this->getRequest();
        $response = $this->response();

        $msg = "";
        $username = $request->get('name');
        $pwd = $request->get('pass');
        $cfpwd = $request->get('passcf');
        $email = $request->get('mail');

        if (!Utils::is_valid_username($username)) {
            $msg .= "<p>" . Language::$phrases['message']['username.invalid'] . "</p>";
        } elseif ($this->model->isUsernameExists($username)) {
            $msg .= "<p>" . Language::$phrases['message']['username.exists'] . "</p>";
        }
        if ($pwd == "") {
            $msg .= "<p>" . Language::$phrases['message']['empty_password'] . "</p>";
        } elseif ($cfpwd != $pwd) {
            $msg .= "<p>" . Language::$phrases['message']['confirm_password_incorrect'] . "</p>";
        }
        if (!Utils::is_valid_email($email)) {
            $msg .= "<p>" . Language::$phrases['message']['email.invalid'] . "</p>";
        } elseif ($this->model->isEmailExists($email)) {
            $msg .= "<p>" . Language::$phrases['message']['email.exists'] . "</p>";
        }

        if ($msg != "") {
            $response->setContent(json_encode(array(
                'status' => 'error',
                'message' => $msg
            )));
        } else {
            $salt = Utils::fetch_user_salt(30);
            $password = Utils::hash_password($pwd, $salt);
            $role = 'subscriber';
            $capability = $this->model->getCapabilityByRole($role);

            $user_id = $this->model->createUser(array(
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'salt' => $salt,
                'role' => $role,
                'capability' => $capability,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            ));

            if ($user_id) {
                $user = $this->model->getUserLogin(array(
                    'username' => $username,
                    'password' => $password,
                    'is_deleted' => 0,
                ));
                if ($user[0]['activation_key'] == "") {
                    unset($user[0]['password']);
                    $user[0]['user_id'] = $user[0]['id'];
                    $user[0]['capability'] = @unserialize($capability);
                    $user[0]['ip_logged_in'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['user_logged_in'] = $user[0];

                    $response->setContent(json_encode(array(
                        'status' => 'success',
                        'message' => Language::$phrases['message']['signup_success'],
                        'redirect' => true
                    )));
                } else {
                    $response->setContent(json_encode(array(
                        'status' => 'success',
                        'message' => Language::$phrases['message']['signup_success']
                    )));
                }
            } else {
                $response->setContent(json_encode(array(
                    'status' => 'error',
                    'message' => Language::$phrases['message']['signup_failure']
                )));
            }
        }
        $response->sendContent();
    }

    function login_process() {
        if (isset($_SESSION['user_logged_in']))
            die();

        $request = $this->getRequest();
        $response = $this->response();

        $msg = "";
        $status = 'error';
        $username = $request->get('name');
        $password = $request->get('pass');
//        $remember = $request->get('remember');

        if (!Utils::is_valid_username($username)) {
            $msg .= "<p>" . Language::$phrases['message']['username.invalid'] . "</p>";
        }
        if ($password == "") {
            $msg .= "<p>" . Language::$phrases['message']['empty_password'] . "</p>";
        }

        if ($msg != "") {
            $response->setContent(json_encode(array(
                'status' => 'error',
                'message' => $msg
            )));
        } else {
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
                    $status = 'success';
                    $msg .= "<p>" . Language::$phrases['message']['login_success'] . "</p>";
//                    if ($remember == "on") {
//                        $user_store = serialize($user[0]);
//                        setcookie('user_login', $user_store, time() + 3600 * 24 * 12, '/'); // 1 year
//                    }
                }
            }
        }
        $response->setContent(json_encode(array(
            'status' => $status,
            'message' => $msg
        )));
        $response->sendContent();
    }

    function logout() {
        unset($_SESSION['user_logged_in']);
        setcookie('user_login', null, -1, '/');

        $this->redirect(Registry::$siteurl);
    }

    function forgot_password() {
        if (isset($_SESSION['user_logged_in']))
            die();

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
                    $option = new OptionAdminModel();
                    $site_option = unserialize($option->getOption('site_option'));
                    $template = $this->getTemplate();
                    $mailer = new Mailer();
                    $subject = $template->assignValue(Language::$phrases['message']['mail.forgotpassword.subject'], array(
                        'name' => ($first_name != "") ? $first_name : $username,
                    ));
                    $body = $template->assignValue(Language::$phrases['message']['mail.forgotpassword.body'], array(
                        'name' => ($first_name != "") ? $first_name : $username,
                        'username' => $username,
                        'password' => $password,
                        'sitename' => $site_option['name'],
                        'login_url' => get_user_login_url(),
                    ));
                    $message = $mailer->createMessage($subject, $body, "text/html", "UTF-8");
                    $message->setTo($email)
                            ->setFrom($site_option['name']);
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

    function buy_code() {
        $option = file_get_contents(DB_PATH . 'options.json');
        $option = json_decode($option);
        $coincode = $option->code_coin;
        $userid = $_SESSION['user_logged_in']['id'];
        $request = $this->getRequest();
        $response = $this->response();

        $msg = "";
        $status = 'error';
        $code_count = $request->get('code_count');
        $coin = $this->model->get_user_coin($userid);
        if ($coin == 0) {
            $msg = 'Tài khoản chưa có tiền, hãy nạp để mua';
        }
        if ($code_count < 1) {
            $msg = 'Hãy chọn số lượng';
        }
        switch ($code_count) {
            case 1: $tien_tru = $coincode->code1;
                break;
            case 3: $tien_tru = $coincode->code3;
                break;
            case 7: $tien_tru = $coincode->code7;
                break;
            case 16: $tien_tru = $coincode->code16;
                break;
            case 24: $tien_tru = $coincode->code24;
                break;
            case 40: $tien_tru = $coincode->code40;
                break;
        }
        if ($coin < $tien_tru) {
            $msg = 'Tài khoản của bạn không đủ để mua, Hãy nạp thêm';
        }
        if ($msg == "") {
            $CodeMD = new CodeModel();
            $codes = $CodeMD->getCode($code_count, array(
                'user_id' => 0,
                'code_status' => 'published'
            ));
            $codemsg = '';
            $array_ids = array();
            $array_codes = array();
            $where = "";
            $count_code_store = count($codes);
            if ($count_code_store < $code_count) {
                $response->setContent(json_encode(array(
                    'status' => $status,
                    'message' => 'Số code trong hệ thống không đủ, vui lòng mua sau.<br/> Bạn mua tối đa ' . $count_code_store . ' code'
                )));
            } else if (is_array($codes) and $count_code_store > 0) {
                foreach ($codes as $code) {
                    array_push($array_ids, $code['id']);
                    array_push($array_codes, $code['code']);
                }
                $ids = implode(",", $array_ids);
                $codes_str = implode(",", $array_codes);
                $codemsg = 'Code của bạn: ' . $codes_str;
                $where = "id IN ($ids)";
                $data = array(
                    'user_id' => $userid,
                    'count' => $code_count,
                    'code' => $codes_str,
                    'amount' => $tien_tru
                );
                $CodeMD->PurchaseCode($data, $where);

                // Tru tien trong tai khoan
                $this->model->updatecoin_buy($tien_tru, $userid);

                $msg = 'Bạn mua thành công: ' . $code_count . ' code. Tài khoản bị trừ ' . $tien_tru;

                $response->setContent(json_encode(array(
                    'status' => 'success',
                    'message' => $msg,
                    'coin' => $coin - $tien_tru,
                    'code' => $codemsg
                )));
            }
        } else {
            $response->setContent(json_encode(array(
                'status' => $status,
                'message' => $msg
            )));
        }

        $response->sendContent();
    }

    function recharge() {
        $msg = "";
        $trang_thai = 'error';

        $request = $this->getRequest();
        $response = $this->response();
        if (empty($_SESSION['user_logged_in'])) {
            $login = 'not-login';
            $response->setContent(json_encode(array(
                'status' => $trang_thai,
                'message' => $msg,
                'login' => $login
            )));
        } else {
            $userid = $_SESSION['user_logged_in']['id'];
            $pin_card = $request->get('pin_card');
            $card_serial = $request->get('card_serial');
            $mang = $request->get('chonmang');
            if (empty($pin_card) || empty($card_serial)) {
                $msg = 'Bạn phải nhập Mã Pin và Serial';
                $response->setContent(json_encode(array(
                    'status' => $trang_thai,
                    'message' => $msg,
                )));
            } else {

                $bk = 'https://www.baokim.vn/the-cao/restFul/send';
                if ($mang == 'MOBI') {
                    $ten = "MOBI";
                } else if ($mang == 'VIETEL') {
                    $ten = "VIETEL";
                } else if ($mang == 'GATE') {
                    $ten = "Gate";
                } else if ($mang == 'VTC') {
                    $ten = "VTC";
                } else {
                    $ten = "VINA";
                }

//Mã MerchantID dang kí trên Bảo Kim
                $merchant_id = '16199';
//Api username 
                $api_username = 'demoppovn';
//Api Pwd d
                $api_password = 'demoppovnkjhgsd6ygHAVouy';
//Mã TransactionId 
                $transaction_id = time();
//mat khau di kem ma website dang kí trên B?o Kim
                $secure_code = '59755038b7a12d13';

                $arrayPost = array(
                    'merchant_id' => $merchant_id,
                    'api_username' => $api_username,
                    'api_password' => $api_password,
                    'transaction_id' => $transaction_id,
                    'card_id' => $ten,
                    'pin_field' => $pin_card,
                    'seri_field' => $card_serial,
                    'algo_mode' => 'hmac',
                );

                ksort($arrayPost);

                $data_sign = hash_hmac('SHA1', implode('', $arrayPost), $secure_code);

                $arrayPost['data_sign'] = $data_sign;

                $curl = curl_init($bk);

                curl_setopt_array($curl, array(
                    CURLOPT_POST => true,
                    CURLOPT_HEADER => false,
                    CURLINFO_HEADER_OUT => true,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTPAUTH => CURLAUTH_DIGEST | CURLAUTH_BASIC,
                    CURLOPT_USERPWD => CORE_API_HTTP_USR . ':' . CORE_API_HTTP_PWD,
                    CURLOPT_POSTFIELDS => http_build_query($arrayPost)
                ));

                $data = curl_exec($curl);

                $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                $result = json_decode($data, true);


                date_default_timezone_set('Asia/Ho_Chi_Minh');
                $time = time();
//$time = time();
                if ($status == 200) {
                    $amount = $result['amount'];
                    // Xu ly thong tin tai day

                    $this->model->updateCoin($amount, $userid);
                    $this->model->recharge_history($userid, $pin_card, $card_serial, $ten, $amount);

                    $file = "carddung.log";
                    $fh = fopen($file, 'a') or die("cant open file");
                    fwrite($fh, "Tai khoan: " . $userid . ", Loai the: " . $ten . ", Menh gia: " . $amount . ", Thoi gian: " . $time);
                    fwrite($fh, "\r\n");
                    fclose($fh);

                    $msg = "<span style='color: green'>Bạn nạp thành công thẻ $ten : $amount</span>";
                    $response->setContent(json_encode(array(
                        'status' => 'success',
                        'message' => $msg,
                    )));
                } else {

                    $error = $result['errorMessage'];

                    $file = "cardsai.log";
                    $fh = fopen($file, 'a') or die("cant open file");
                    fwrite($fh, "Tai khoan: " . $userid . ", Ma the: " . $pin_card . ", Seri: " . $card_serial . ", Noi dung loi: " . $error . ", Thoi gian: " . $time);
                    fwrite($fh, "\r\n");
                    fclose($fh);
                    $msg = "<span style='color: red'>Nạp thẻ không thành công</span>";
                    $response->setContent(json_encode(array(
                        'status' => $trang_thai,
                        'message' => $msg,
                    )));
                }
            }
        }
        $response->sendContent();
    }

    function contact() {
        $request = $this->getRequest();
        $response = $this->response();

        $name = $request->get('name');
        $email = $request->get('email');
        $message = $request->get('message');

        if (empty($name) or empty($email) or empty($message)) {
            $response->setContent(json_encode(array(
                'status' => 'error',
                'message' => 'Vui lòng nhập đủ thông tin'
            )));
        } else {
            $option = file_get_contents(DB_PATH . 'options.json');
            $option = json_decode($option);
            $site_option = $option->site_option;
            $body = <<<HTML
<p>Họ và tên: {$name}</p>
<p>Email: {$email}</p>
<p>Nội dung: </p>
<p>{$message}</p>
HTML;
            $mailer = new Mailer();
            $message2 = $mailer->createMessage('Liên hệ - ' . $site_option->name, $body, "text/html", "UTF-8");
            $message2->setTo($site_option->admin_email)
                    ->setFrom($site_option->admin_email, $site_option->name)
                    ->addReplyTo($email, $name);
            $mailer->createMailer()->send($message2);

            $response->setContent(json_encode(array(
                'status' => 'success',
                'message' => 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm'
            )));
        }

        $response->sendContent();
    }

}
