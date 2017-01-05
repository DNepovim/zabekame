<?php

namespace App\Presenters;

use Nette;
use App\Model\SongManager;
use App\Model\SongbookManager;


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

	/**
	 * Get list of related guids
	 * @return array
	 */
	protected function getGuids()
	{

		$userID = $this->getUser()->id;

		$songManager = new SongManager($this->database);
		$songbookManager = new SongbookManager($this->database);

		$guids = array_merge($songManager->getListOfUserGuids($userID), $songbookManager->getListOfUserGuids($userID));

		return $guids;
	}
}
