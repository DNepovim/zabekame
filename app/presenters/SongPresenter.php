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
	public function renderDetail( $username, $songbook = '', $guid )
	{


		$songItem = $this->itemSwitcher($username, $guid);

		$this->template->song = $songItem;
		$this->template->editable = $songItem->userID == $this->user->id;

		$songbookItem = new SongbookItem($this->database);
		$songbookItem->getSongbook($username, $guid);
		$songbookItem->getSongs();

		$this->template->songbook = $songbookItem;

	}

	/**
	 * Render edit
	 *
	 */
	public function actionEdit( $username, $songbook = '', $guid )
	{

		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejdřív se musíš přihlásit.', 'warning');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$this->itemSwitcher($username, $guid, 'edit');

		$songItem = new SongItem($this->database);
		$songItem->getSong($username, $guid);

		if ($songItem->userID !== $this->user->getID()) {

			$username = $songItem->username;
			$this->flashMessage('Píseň může editovat pouze vlastník', 'warning');
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
			$this->flashMessage('Nejdřív se musíš přihlásit.', 'warning');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$guids = $this->getGuids();

		$this->template->guids = $guids;
	}

	/**
	 * Remove song
	 */
	public function actionRemove($username, $guid)
	{

		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejdřív se musíš přihlásit.', 'warning');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$this->itemSwitcher($username, $guid, 'remove');

		$songManager = new SongManager($this->database);

		if ($songManager->remove($username, $guid)) {
			$this->flashMessage('Píseň byla smazána.', 'success');
		} else {
			$this->flashMessage('Píseň neexistuje.', 'error');
		}

		$this->redirect('User:dashboard');

	}

	private function itemSwitcher($username, $guid, $action = 'detail') {

		$songItem = new SongItem($this->database);
		$songbookItem = new SongbookItem($this->database);

		if ($songItem->getSong($username, $guid)) {
			return $songItem;
		} elseif ($songbookItem->getSongbook($username,$guid)) {
			$this->forward('Songbook:' . $action, ['username' => $username, 'songbook' => $guid]);
		} else {
			$this->flashMessage('Na této url není žádná písnička ani zpěvník. Možná byla smazána, nebo přejmenována.', 'error');
			$this->redirect('User:dashboard');
			exit;
		};
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
			$this->redirect('Song:detail',['username' => $username, 'item' => $songguid]);
		},$userID, $username, $songbooksList);
	}

	/**
	 * Song edit form factory.
	 * @id string id of edited song
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongEditForm()
	{
		return $this->songEditFactory->create(function ($username, $songguid) {
			$this->flashMessage('Píseň byla uložena.', 'success');
			$this->redirect('Song:detail',['username' => $username, 'guid' => $songguid]);
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
