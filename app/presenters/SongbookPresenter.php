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

	/** @var Forms\SongbookEditFormFactory @inject */
	public $songbookEditFactory;

	public $id;
	/**
	 * Render songbook add page
	 */
	public function renderAdd()
	{
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejdřív se musíš přihlásit.');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$guids = $this->getGuids();

		$this->template->guids = $guids;
	}

	/**
	 * Render songbook edit page
	 */
	public function renderEdit($id)
	{
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejdřív se musíš přihlásit.');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$songbook = new SongbookItem($this->database);
		$songbook->getSongbook($id);
		$this->id = $id;

		if ($songbook->user->id !== $this->user->id) {
			$this->flashMessage('Píseň může editovat pouze vlastník');
			$this->redirect('Song:detail', $id);
		}
	}

	/**
	 * Action remove
	 */
	public function actionRemove($songbook)
	{

	}

	/**
	 * Render songbook detail
	 */
	public function renderDetail($songbook)
	{
		$songbookItem = new SongbookItem($this->database);
		$songbookItem->getSongbook($songbook);

		$ids = $songbookItem->getSongsIDFromSongbook($songbook);

		$songsManager = new SongManager($this->database);

		$this->template->title = $songbookItem->title;
		$this->template->id = $songbookItem->id;
		$this->template->songs = $songsManager->getSongsById($ids);
	}

	/**
	 * Render songbook with uncategorized songs
	 *
	 */
	public function renderOthers($user)
	{
		$songManager = new SongManager($this->database);
		$this->template->songs = $songManager->getOthersSongs($user);
		$this->template->title = 'Ostatní';
		$this->template->id = false;

		$this->setView('detail');

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

	/**
	 * Songbook edit form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongbookEditForm()
	{
		$songbook = new SongbookItem($this->database);
		$songbook->getSongbook($this->id);

		return $this->songbookEditFactory->create(function ($guid) {
			$this->redirect('Songbook:detail', $guid);
		}, $songbook);
	}

}
