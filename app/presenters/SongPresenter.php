<?php

namespace App\Presenters;

use App\Model\SongbookItem;
use App\Model\SongbookManager;
use App\Model\SongItem;
use App\Model\SongManager;
use Nette;
use App\Forms;


class SongPresenter extends BasePresenter
{
	/** @var Forms\SongFormFactory @inject */
	public $songFactory;

	/** @var Forms\SongEditFormFactory @inject */
	public $songEditFactory;

	/** @var Forms\SongImportFormFactory @inject */
	public $songImportFactory;

	public $id;

	/**
	 * Render default list */
	public function renderList( $id = null )
	{
		$songManager = new SongManager($this->database);
		$this->template->songs = $songs = $songManager->getUsersSongs($this->getUser()->id);
	}

	/**
	 * Render detail
	 */
	public function renderDetail( $id, $songbook = -1 )
	{
		$songItem = new SongItem($this->database);
		$songItem->getSong($id);
		$this->template->song = $songItem;
		$this->template->editable = $songItem->user->id == $this->user->id;

		if ($songbook == -1) {
			$this->template->backToSongbook = false;
		} else if ($songbook == 0) {
			$this->template->backToSongbook = 'others';
		} else {
			$songbookItem = new SongbookItem($this->database);
			$songbookItem->getSongbook($songbook);

			$this->template->backToSongbook = $songbookItem;
		}
	}

	/**
	 * Render edit
	 */
	public function renderEdit( $id, $songbook = -1 )
	{
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejdřív se musíš přihlásit.');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$song = new SongItem($this->database);
		$song->getSong($id);

		if ($song->user->id !== $this->user->id) {
			$this->flashMessage('Píseň může editovat pouze vlastník');
			$this->redirect('Song:detail', $id);
		}


		if ($songbook == -1) {
			$this->template->backToSongbook = false;
		} else if ($songbook == 0) {
			$this->template->backToSongbook = 'others';
		} else {
			$songbookItem = new SongbookItem($this->database);
			$songbookItem->getSongbook($songbook);

			$this->template->backToSongbook = $songbookItem;
		}

		$this->template->id = $this->id = $id;

		$guids = $this->getGuids($id);

		$this->template->guids = $guids;
	}

	/**
	 * Render edit
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
	 * Remove song
	 */
	public function actionRemove($id)
	{

	}

	/**
	 * Song form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongForm()
	{
		$user = $this->getUser()->id;
		$songbooks = new SongbookManager($this->database);
		$songbooksList = $songbooks->getUsersSongbooks($user);

		return $this->songFactory->create(function ($guid) {
			$this->redirect('Song:detail', $guid);
		}, $user, $songbooksList);
	}

	/**
	 * Song edit form factory.
	 * @id string id of edited song
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongEditForm()
	{
		$song = new SongItem($this->database);
		$song->getSong($this->id);

		$user = $this->getUser()->id;

		$songbookManager = new SongbookManager($this->database);
		$songbooksList = $songbookManager->getUsersSongbooks($user);

		return $this->songEditFactory->create(function ($guid) {
			$this->redirect('Song:edit', $guid);
		}, $song, $songbooksList);
	}

	/**
	 * Song form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongImportForm()
	{
		$user = $this->getUser()->id;

		return $this->songImportFactory->create(function ($guid) {
			$this->redirect('Song:edit', $guid);
		}, $user);

	}

}

