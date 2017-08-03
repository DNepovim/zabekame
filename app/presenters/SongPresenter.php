<?php

namespace App\Presenters;

use App\Model\SongbookItem;
use App\Model\SongbookManager;
use App\Model\SongItem;
use App\Model\SongManager;
use App\Model\UserManager;
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
	public function renderDetail( $username, $songbook = '', $songguid )
	{
		$this->redirect('Songbook:detail',['username' => $username, 'songbook' => $songbook, 'songguid' => $songguid]);


		$songItem = new SongItem($this->database);

		if ($songItem->getSong($username, $songguid)) {
			$this->template->song = $songItem;
			$this->template->editable = $songItem->userID == $this->user->id;
		} else {
			$this->flashMessage('Tato písnička neexistuje. Možná byla smazána, nebo přejmenována.');
			$this->redirect('User:dashboard',$username);
			exit;
		};

		$songbookItem = new SongbookItem($this->database);
		$songbookItem->getSongbook($username,$songbook);
		$songbookItem->getSongs();
		$this->template->songbook = $songbookItem;

	}

	/**
	 * Render edit
	 *
	 */
	public function actionEdit( $username, $songbook = '', $guid )
	{

		$i = 0;
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejdřív se musíš přihlásit.');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$songItem = new SongItem($this->database);
		$songItem->getSong($username, $guid);

		if ($songItem->userID !== $this->user->getID()) {

			$username = $songItem->username;
			$this->flashMessage('Píseň může editovat pouze vlastník');
			$this->redirect('Song:detail', $username, $guid);
		}


		$songbookItem = new SongbookItem($this->database);
		$songbookItem->getSongbook($username, $songbook);

		$songbookManager = new SongbookManager($this->database);
		$this->songbooksList = $songbookManager->getUsersSongbooks($this->user->id);

		$this->template->song = $this->song = $songItem;
		$this->template->songbook = $this->songbook = $songbookItem;
		$this->template->guids = $this->getGuids();

	}


	/**
	 * Render add
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
	public function actionRemove($guid)
	{
		$songManager = new SongManager($this->database);

		$userID = $this->getUser()->id;

		if ($songManager->remove($userID, $guid)) {
			$this->flashMessage('Píseň byla smazána.');
		} else {
			$this->flashMessage('Píseň neexistuje.');
		}

		$this->redirect('User:dashboard');

	}

	/**
	 * Song form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongForm()
	{
		$userID = $this->getUser()->id;
		$userManager = new UserManager($this->database);
		$username = $userManager->getNickByID($userID);

		$songbooks = new SongbookManager($this->database);
		$songbooksList = $songbooks->getUsersSongbooks($userID);

		return $this->songFactory->create(function ($username, $songguid) {
			$this->redirect('Song:detail',['username' => $username, 'songguid' => $songguid]);
		},$userID, $username, $songbooksList);
	}

	/**
	 * Song edit form factory.
	 * @id string id of edited song
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongEditForm()
	{
		return $this->songEditFactory->create(function ($username,$songguid) {
			$this->redirect('Song:detail',['username' => $username, 'songguid' => $songguid]);
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
