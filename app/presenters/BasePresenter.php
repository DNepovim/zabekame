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
	protected function getGuids($id=null)
	{

		$user = $this->getUser()->id;

		$songManager = new SongManager($this->database);
		$songbookManager = new SongbookManager($this->database);

		$guids = array_merge($songManager->getListOfUserGuids($user), $songbookManager->getListOfUserGuids($user));

		if ($id) {
			if (is_numeric($id)) {
				$guid = $songManager->getGuidById($id);
			} else {
				$guid = $id;
			}
			$guids = array_diff($guids, array($guid));
		}

		return $guids;
	}
}
