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

	public $song;
	public $songbook;
	public $songbooksList;

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
	public function renderDetail( $user, $songbook = '', $song )
	{

		$songItem = new SongItem($this->database);

		if ($songItem->getSong($user, $song)) {
			$this->template->song = $songItem;
			$this->template->editable = $songItem->user->id == $this->user->id;
		} else {
			$this->flashMessage('Tato písnička neexistuje. Možná byla smazána, nebo přejmenována.');
			$this->redirect('User:dashboard',$user);
			exit;
		};

		$songbookItem = new SongbookItem($this->database);
		$songbookItem->getSongbook($user,$songbook);
		$songbookItem->getSongs();
		$this->template->songbook = $songbookItem;

	}

	/**
	 * Render edit
	 *
	 */
	public function actionEdit( $username, $songbook = '', $songguid )
	{
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejdřív se musíš přihlásit.');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$songItem = new SongItem($this->database);
		$songItem->getSong($username, $songguid);

		if ($songItem->user->id !== $this->user->id) {
			$this->flashMessage('Píseň může editovat pouze vlastník');
			$this->redirect('Song:detail', $id);
		}

		$songbookItem = new SongbookItem($this->database);
		$songbookItem->getSongbook($songbook);

		$songbookManager = new SongbookManager($this->database);
		$this->songbooksList = $songbookManager->getUsersSongbooks($this->user->id);

		$this->template->song = $this->song = $songItem;
		$this->template->songbook = $this->songbook = $songbookItem;
		$this->template->guids = $this->getGuids();
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

		return $this->songEditFactory->create(function ($guid) {
			$this->redirect('Song:edit', $guid);
		}, $this->song, $this->songbooksList);
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
