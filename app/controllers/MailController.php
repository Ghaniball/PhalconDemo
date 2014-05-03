<?php

use Zend\Mail\Message,
	Zend\Mail\Transport\Smtp as SmtpTransport,
	Zend\Mime\Message as MimeMessage,
	Zend\Mime\Part as MimePart,
	Zend\Mail\Transport\SmtpOptions,
	Phalcon\Logger\Adapter\File as FileLogger;

class MailController extends ControllerBase {

	public function indexAction() {
		echo 'mail/index';
	}

	public function sendAction() {
	//	$settings = $this->config->mail;
		$message = new Message();
		$message->setBody('This is the text of the email.')
				->setFrom('ghanibalx@gmail.com', 'Me')
				->addTo('ghaniball@mail.ru', 'Ivan')
				->setSubject('TestSubject');

		// Setup SMTP transport using LOGIN authentication
		$transport = new SmtpTransport();
		$options = new SmtpOptions(array(
			'host' => 'smtp.gmail.com',
			'connection_class' => 'login',
			'connection_config' => array(
				'ssl' => 'tls',
				'username' => 'ghanibalx@gmail.com',
				'password' => ''
			),
			'port' => 587,
		));

		$html = new MimePart('<b>heii, <i>sorry</i>, i\'m going late</b>');
		$html->type = "text/html";

		$body = new MimeMessage();
		$body->addPart($html);

		$message->setBody($body);

		$transport->setOptions($options);
		$transport->send($message);
		
		$logger = new FileLogger($this->config->logPath . date('d-m-Y') . '.log');
		$logger->log($message->getBodyText());
		$logger->close();
	}
}
