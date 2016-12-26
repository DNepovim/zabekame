<?php

namespace App\Presenters;

use App\Model\SongItem;
use App\Model\SongManager;
use Nette;
use App\Forms;




class SongbookPresenter extends BasePresenter
{
	/** @var Forms\SongbookFormFactory @inject */
	public $songbookFactory;

	public function beforeRender() {
		parent::beforeRender();

		if (!$this->getUser()->loggedIn) {
			$this->redirect('Sign:in');
		}
	}

	/**
	 * Render default list
	 */
	public function renderList( $id = null )
	{
		$songbookManager = new SongbookManager($this->database);
		$this->template->songbookss = $songs = $songbookManager->getUsersSongbooks($this->getUser()->id);
	}

	/**
	 * Songbook form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongbookForm()
	{
		$user = $this->getUser()->id;

		return $this->songbookFactory->create(function ($guid) {
			$this->redirect('Songbook:detail', $guid);
		}, $user);
	}

}
