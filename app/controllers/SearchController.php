<?php

use Zend\Http\Client;
use Phalcon\Logger\Adapter\File as FileLogger;

class SearchController extends ControllerBase {

	public function indexAction() {

	}

	public function makeAction() {

		$form = new SearchForm();

		if ($this->request->isPost() &&
			$form->isValid($this->request->getPost()))
		{
			$keyword = $this->request->getPost("keyword");
			$domain = $this->getDomain($this->request->getPost("domain"));

			$response = $this->makeRequest($keyword);
			$results = $this->getResults($response);
			$mes = $this->getMessage($results, $keyword, $domain);

			$this->sendEmail($results, $keyword, $domain);

			$this->log($results, $keyword, $domain);
			echo $this->view->getRender('search', 'feedback', array(
				'message' => $mes,
			));
			$this->view->disable();
		}

		$this->view->form = $form;
	}

	private function getDomain($domain) {
		$domain = str_replace(array('http://', 'https://', 'www.'), "", $domain);
		
		$pos = strpos($domain, "/");
		
		if ($pos !== FALSE) {
			return substr($domain, 0, $pos);
		} else {
			return $domain;
		}
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
			return array('body' => sprintf($messages->requestFails->body, $keyword),
				'head' => $messages->requestFails->head,);
		} else {
			foreach ($results as $key => $result) {
				if (strpos($result->visibleUrl, $domain) !== FALSE) {
					return array('body' => sprintf($messages->foundInResults->body, $keyword),
						'head' => $messages->foundIdResults->head,);
				}
			}

			return array('head' => sprintf($messages->notFoundInResults->head, $keyword),
				'body' => $messages->notFoundInResults->body);
		}
	}

	/**
	 * @param Array $results
	 * @param String $keyword
	 * @param String $domain
	 * @return NULL
	 */
	private function sendEmail($results, $keyword, $domain) {
		$html = $this->view->getRender('templates', 'mail', array(
			'results' => $results,
			'keyword' => $keyword,
			'domain' => $domain
				), function($view) {
			$view->setRenderLevel(Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
		});
		
		$cmd = $this->buildCmd($html);
		
		pclose(popen($cmd, 'r'));
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
					"\n'title': " . $result->title .
					"\n'content': " . $result->content;
		}

		$logger->log('Keyword: [' . $keyword . ']');
		$logger->log('Domain: [' . $domain . ']');
		$logger->log('Results: ' . $selectedResults .
				"\n\n======================================================\n");
		$logger->close();
	}

	private function buildCmd($html) {
		$phpExe = $this->config->phpExe;
		
		if ($this->is_windows()){
			$prefix = 'start /B ';
		} else {
			$prefix = '';
		}
		
		return $prefix . $phpExe . ' -r "file_get_contents(\'' . $this->config->domainName . $this->config->application->baseUri . 'mail/send/?html=' . urlencode($html) . '\');"';
	}
	
	function is_windows() {
		if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32') {
			return TRUE;
		}
		return FALSE;
	}
}
