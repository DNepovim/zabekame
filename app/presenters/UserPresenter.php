<?php

namespace App\Presenters;

use App\Model\SongbookManager;
use App\Model\SongManager;
use App\Model\UserManager;
use Nette;
use App\Forms;


class UserPresenter extends BasePresenter
{
	public function beforeRender() {
		parent::beforeRender();

		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejdřív se musíš přihlásit.');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}
	}

	/**
	 * Render default list */
	public function renderDashboard()
	{

		$userid = $this->user->id;

		$userManager = new UserManager($this->database);
		$this->template->username = $userManager->getNickByID($userid);


		$songbookManager = new SongbookManager($this->database);
		$this->template->songbooks = $songbookManager->getUsersSongbooks($userid);

		$songManager = new SongManager($this->database);
		$this->template->songs = $songManager->getUsersSongs($userid);
	}

	public function renderEdit($username)
	{
		echo 'user edit';
		exit;
	}
}
