<?php

namespace App\Presenters;

use App\Model\SongItem;
use App\Model\SongManager;
use Nette;
use App\Forms;


class SongPresenter extends BasePresenter
{
	/** @var Forms\SongFormFactory @inject */
	public $songFactory;


	/**
	 * Render default list
	 */
	public function renderList( $id = null )
	{
		$songManager = new SongManager($this->database);
		$this->template->songs = $songs = $songManager->getUsersSongs($this->getUser()->id);
	}

	/**
	 * Song form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongForm()
	{
		$user = $this->getUser()->id;

		return $this->songFactory->create(function () {
			$this->redirect('Homepage:');
		}, $user);
	}

}
