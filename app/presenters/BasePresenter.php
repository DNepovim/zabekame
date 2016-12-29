<?php

namespace App\Presenters;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Context */
	public $database;

	public function __construct( Nette\Database\Context $database ) {
		$this->database = $database;
	}

	public function beforeRender() {
		parent::beforeRender();

		$user = $this->getUser();

		if ($user->isLoggedIn()) {
			$this->template->currentUser = $user->identity->getRoles();
		}

	}

}
