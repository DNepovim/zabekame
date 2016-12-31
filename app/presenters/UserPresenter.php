<?php

namespace App\Presenters;

use App\Model\SongbookManager;
use App\Model\SongManager;
use Nette;
use App\Forms;


class UserPresenter extends BasePresenter
{

	/**
	 * Render default list */
	public function renderDashboard()
	{
		$songbookManager = new SongbookManager($this->database);
		$this->template->songbooks = $songbookManager->getUsersSongbooks($this->getUser()->id);
	}
}

