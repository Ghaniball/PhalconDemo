<?php

use Zend\Http\Client;
use Zend\Mail\Message,
	Zend\Mail\Transport\Smtp as SmtpTransport,
	Zend\Mime\Message as MimeMessage,
	Zend\Mime\Part as MimePart,
	Zend\Mail\Transport\SmtpOptions,
	Phalcon\Logger\Adapter\File as FileLogger;

class SearchController extends ControllerBase {

	public function indexAction() {
		
	}

	public function makeAction() {

		$form = new SearchForm();

		if ($this->request->isPost()) {

			if ($form->isValid($this->request->getPost())) {


				$keyword = $this->request->getPost("keyword");
				$domain = $this->request->getPost("domain");

				if (!empty($keyword) && !empty($domain)) {
					$response = $this->makeRequest($keyword);
					$results = $this->getResults($response);
					$mes = $this->getMessage($results, $keyword, $domain);

					$this->sendEmail($results, $keyword, $domain);
					//$this->log($results, $keyword, $domain);
				} else {
					$mes = "No Data";
				}
			}
		}
		$this->view->form = $form;
		//$this->view->setVar("message", $mes);
	}

	/**
	 * @param String $keyword
	 * @return \Zend\Http\Response
	 */
	private function makeRequest($keyword) {
		$uri = "http://ajax.googleapis.com/ajax/services/search/web";

		$client = new Client(
				$uri, array(
			'maxredirects' => 10,
			'timeout' => 60
		));
		$client->setParameterGet(array(
			'q' => $keyword,
			'rsz' => 'large',
			'v' => '1.0',
			'start' => '0',
			'hl' => 'de',
			'lr' => 'lang_de',
		));

		return $client->send();
	}

	/**
	 * @param \Zend\Http\Response $response
	 * @return Array
	 */
	private function getResults(\Zend\Http\Response $response) {
		$json = $response->getBody();
		$res = json_decode($json);
		return $res->responseData->results;
	}

	/**
	 * @param Array $results
	 * @param String $keyword
	 * @param String $domain
	 * @return String
	 */
	private function getMessage($results, $keyword, $domain) {
		$messages = $this->config->messages;

		if (empty($results)) {
			return sprintf($messages->requestFails, $keyword);
		} else {
			foreach ($results as $key => $result) {
				if ($domain == $result->visibleUrl) {
					return sprintf($messages->foundInResults, $keyword);
				}
			}

			return sprintf($messages->notFoundInResults, $keyword);
		}
	}

	/**
	 * @param Array $results
	 * @param String $keyword
	 * @param String $domain
	 * @return NULL
	 */
	private function sendEmail($results, $keyword, $domain) {
		$mailCfg = $this->config->mail;
		
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

		$html = new MimePart($this->view->getRender('templates', 'mail', array(
			'results' => $results,
			'keyword' => $keyword,
			'domain' => $domain
		), function($view) {
            $view->setRenderLevel(Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        }));
		
		$html->type = "text/html";

		$body = new MimeMessage();
		$body->addPart($html);

		$message->setBody($body);

		$transport->setOptions($options);
		$transport->send($message);
	}

	/**
	 * @param Array $results
	 * @param String $keyword
	 * @param String $domain
	 * @return NULL
	 */
	private function log($results, $keyword, $domain) {
		// 1GB of logs will cover ~ 350k requests
		
		$logger = new FileLogger($this->config->logPath . date('d-m-Y') . '.log');
		
		$selectedResults = "";
		
		foreach ($results as $result) {
			$selectedResults .= 
				"\n'url': " . $result->url .
				"\n'title': " . $result->title.
				"\n'content': " . $result->content;
		}
		
		$logger->log('Keyword: [' . $keyword . ']');
		$logger->log('Domain: [' . $domain . ']');
		$logger->log('Results: ' . $selectedResults . 
					"\n\n======================================================\n");
		$logger->close();
	}
}
