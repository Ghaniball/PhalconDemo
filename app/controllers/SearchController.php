<?php

use Zend\Http\Client;
use Zend\Mail\Message,
	Zend\Mail\Transport\Smtp as SmtpTransport,
	Zend\Mime\Message as MimeMessage,
	Zend\Mime\Part as MimePart,
	Zend\Mail\Transport\SmtpOptions,
	Phalcon\Logger\Adapter\File as FileLogger;

class SearchController extends ControllerBase
{
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
				
				$this->sendEmail($mes);
				$this->log($results, $keyword, $domain);
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
                                        $uri,
                                        array(
                                                'maxredirects' => 10,
                                                'timeout'      => 60
                                    ));
		$client->setParameterGet(array(
			'q'	=> $keyword,
			'rsz'	=> 'large',
			'v'     => '1.0',
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
	 * @param String $mes
	 * @return String
	 */
	private function sendEmail($mes) {
            $settings = $this->config->mail;
            $message = new Message();
            $message->setBody('This is the text of the email.')
                            ->setFrom('', 'Sender')
                            ->addTo('', 'Report')
                            ->setSubject('TestSubject');

            // Setup SMTP transport using LOGIN authentication
            $transport = new SmtpTransport();
            $options = new SmtpOptions(array(
                    'host' => 'smtp.mail.yahoo.com',
                    'connection_class' => 'login',
                    'connection_config' => array(
                            'ssl' => 'ssl',
                            'username' => '',
                            'password' => ''
                    ),
                    'port' => 465,
            ));

            $html = new MimePart($mes);
            $html->type = "text/html";

            $body = new MimeMessage();
            $body->addPart($html);

            $message->setBody($body);

            $transport->setOptions($options);
            $transport->send($message);
            //$this->log($message);
            
	}

	/**
	 * @param String $mes
	 * @param Array $results
	 * @param String $keyword
	 * @param String $domain
	 * @return String
	 */
	private function log($results, $keyword, $domain) {
            $logger = new FileLogger($this->config->logPath . date('d-m-Y') . '.log');
            $str = " || ";
            $status = "No";
            foreach ($results as $key => $result) {
                            $str .= $result->visibleUrl." | ";
                            if ($domain == $result->visibleUrl) {
                                    $status = "Yes";
                            }
                    }
            $str .= " || keyword = $keyword || domain = $domain || $status";
            $logger->log($str);
            $logger->close();
	}

}
