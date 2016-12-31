<?php

namespace App\Presenters;

use App\Model\SongbookManager;
use App\Model\SongbookItem;
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
	 * Render songbooks list
	 */
	public function renderList()
	{
		$songbookManager = new SongbookManager($this->database);
		$this->template->songbooks = $songs = $songbookManager->getUsersSongbooks($this->getUser()->id);
	}

	/**
	 * Render songbook detail
	 */
	public function renderDetail($id)
	{
		$songbookItem = new SongbookItem($this->database);
		$songbookItem->getSongbook($id);

		$ids = $songbookItem->getSongsIDFromSongbook($id);

		$songsManager = new SongManager($this->database);

		$this->template->songbook = $songbookItem;
		$this->template->songs = $songsManager->getSongsById($ids);
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
