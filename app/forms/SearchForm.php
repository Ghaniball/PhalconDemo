<?php

/**
 * Description of SearchForm
 *
 * @author igodorogea
 */
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class SearchForm extends Form {

	public function initialize($entity = null, $options = null) {
		$keyword = new Text('keyword',array(
                    'placeholder' => "suchbegriffe, keywords",
                ));

		//$keyword->setLabel('Keyword');

		$keyword->addValidators(array(
			new PresenceOf(array(
				'message' => 'The keyword is required'
					)),
			new StringLength(array(
				'min' => 3,
				'messageMinimum' => 'Keyword is too short. Minimum 3 characters'
					)),
		));

		$this->add($keyword);

		$doamin = new Text('domain', array(
                    'placeholder' => "www.ihre-webseite.com",
                ));

		//$doamin->setLabel('Domain');

		$doamin->addValidators(array(
			new PresenceOf(array(
				'message' => 'The domain is required'
					)),
			new RegexValidator(array(
				'pattern' => '/^(?:https?:\/\/)?(?:[a-z0-9-]+\.)*((?:[a-z0-9-]+\.)[a-z]+)(.*)/',
				'message' => 'The domain name is invalid'
					)),
		));
		
		$this->add($doamin);
/*
		$di = Phalcon\DI::getDefault();
		$security = $di['security'];
		// CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(new Identical(array(
			'value' => $security->getSessionToken(),
			'message' => 'CSRF validation failed'
		)));

		$this->add($csrf);
*/
		// Submit
		$submit = new Submit('Check', array(
			"value" => "Analyse Starten",
		));

		$this->add($submit);
	}

	/**
	 * Prints messages for a specific element
	 */
	public function messages($name) {
		if ($this->hasMessagesFor($name)) {
			foreach ($this->getMessagesFor($name) as $message) {
				$this->flash->error($message);
			}
		}
	}
}
