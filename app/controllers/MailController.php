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

	public function sendAction()
	{
		$mailCfg = $this->config->mail;
		
		$text = $this->request->getQuery('html');

		$html = new MimePart($text);
		$html->type = "text/html";

		$body = new MimeMessage();
		$body->addPart($html);

		$message = new Message();
		$message->setFrom($mailCfg->from->mail, $mailCfg->from->name)
				->addTo($mailCfg->to->mail, $mailCfg->to->name)
				->setSubject($mailCfg->subject);

		// Setup SMTP transport using LOGIN authentication
		$transport = new SmtpTransport();
		$options = new SmtpOptions(array(
			'host' => $mailCfg->smtp->host,
			'connection_class' => 'login',
			'connection_config' => array(
				'ssl' => $mailCfg->smtp->security,
				'username' => $mailCfg->smtp->username,
				'password' => $mailCfg->smtp->password
			),
			'port' => $mailCfg->smtp->port,
		));

		$message->setBody($body);

		$transport->setOptions($options);
		
		try {
			$transport->send($message);
		} catch (Exception $ex) {
			$logger = new FileLogger($this->config->logPath . 'mail_send_error' . date('d-m-Y') . '.log');
			$logger->error($e->getMessage());
			$logger->error($e->getTraceAsString());
			$logger->close();
		}
	}
}
