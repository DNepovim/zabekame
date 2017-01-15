<?php

namespace App\Presenters;

use App\Model\SongbookManager;
use App\Model\SongManager;
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
	public function renderDashboard($username)
	{
		$songbookManager = new SongbookManager($this->database);
		$this->template->songbooks = $songbookManager->getUsersSongbooks($this->getUser()->id);

		$songManager = new SongManager($this->database);
		$this->template->songs = $songManager->getUsersSongs($this->getUser()->id);
	}

	public function renderEdit($username)
	{
		echo 'user edit';
		exit;
	}
}
