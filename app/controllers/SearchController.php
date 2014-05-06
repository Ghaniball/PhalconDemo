<?php

use Zend\Http\Client;

class SearchController extends ControllerBase
{
	public function indexAction() {

	}

	public function makeAction() {
		
		$form = new SearchForm();
		
		if ($this->request->isPost()) {
			
			if ($form->isValid($this->request->getPost())) {
				
			}
			/*
			$keyword = $this->request->getPost("keyword");
			$domain = $this->request->getPost("domain");

			if (!empty($keyword) && !empty($domain)) {
				$response = $this->makeRequest($keyword);
				$results = $this->getResults($response);
				
				$mes = $this->getMessage($results, $keyword, $domain);
				
				$this->sendEmail($results, $keyword, $domain, $mes);
				$this->log($results, $keyword, $domain, $mes);
			} else {
				$mes = "No Data";
			}*/
			
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
						$uri,
						array(
							'maxredirects' => 10,
							'timeout'      => 60
						));
		$client->setParameterGet(array(
			'q'		=> $keyword,
			'rsz'	=> 'large',
			'v'		=> '1.0',
			'start'	=> '0',
			'hl'	=> 'de',
			'lr'	=> 'lang_de',
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
	 * @param String $mes
	 * @return String
	 */
	public function sendEmail($results, $keyword, $domain, $mes) {
		// TODO
	}

	/**
	 * @param Array $results
	 * @param String $keyword
	 * @param String $domain
	 * @param String $mes
	 * @return String
	 */
	public function log($results, $keyword, $domain, $mes) {
		// TODO
	}

}
