<?php

class Mailer {
    
    private $transport;

    public function __construct() {
        require SYS_PATH . 'libraries' . DS . 'swiftmailer' . DS . 'swift_required.php';
        
        $config = Registry::$settings['config'];
        
        // Create the Transport
        $this->transport = Swift_SmtpTransport::newInstance($config['swiftmailer']['host'], $config['swiftmailer']['port'], $config['swiftmailer']['security'])
                ->setUsername($config['swiftmailer']['username'])
                ->setPassword($config['swiftmailer']['password']);
    }
    /**
     * Create the Mailer using your created Transport
     */
    public function createMailer() {
        return Swift_Mailer::newInstance($this->transport);
    }
    
    public function createMessage($subject = null, $body = null, $contentType = "text/html", $charset = "UTF-8") {
        return Swift_Message::newInstance($subject, $body, $contentType, $charset);
    }

}
