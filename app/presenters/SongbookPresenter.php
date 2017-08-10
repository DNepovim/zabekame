<?php

namespace App\Presenters;

use App\Model\SongbookManager;
use App\Model\SongbookItem;
use App\Model\SongManager;
use App\Model\SongItem;
use App\Model\UserManager;
use Nette\Application\UI;
use Nette;
use App\Forms;



class SongbookPresenter extends BasePresenter
{
	protected $songbookItem;

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
			$this->flashMessage('Nejdřív se musíš přihlásit.', 'warning');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$guids = $this->getGuids();

		$this->template->guids = $guids;
	}

	/**
	 * Render songbook edit page
	 */
	public function actionEdit($username, $guid)
	{
		
		
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejdřív se musíš přihlásit.', 'warning');
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}

		$songbookItem = new SongbookItem($this->database);
		$songbookItem->getSongbook($username, $guid);
		$songbookItem->getSongs();

		$this->songbookItem = $songbookItem;
		$this->template->songs = $songbookItem->songs;

		if ($songbookItem->user->id !== $this->user->id) {
			$this->flashMessage('Zpěvník může editovat pouze vlastník', 'warning');
			$this->redirect('Song:detail', ['username' => $username, 'guid' => $guid]);
		}
	}

	/**
	 * Action remove
	 */
	public function actionRemove($songbook)
	{
		dump('remove songbook'); exit;
	}

	/**
	 * Render songbook detail
	 */
	public function renderDetail($username, $guid = null, $view = 'detail')
	{

		$userMnanger = new UserManager($this->database);
		if ($userMnanger->existUsername($username)) {

			if (!$guid) {
				$songbookManager = new SongbookManager($this->database);
				$songbook = $songbookManager->getDefaultSongbook($username);
			}

			$songbookItem = new SongbookItem($this->database);
			$songbookItem->getSongbook($username,$songbook);
			$songbookItem->getSongs();

			$songsManager = new SongManager($this->database);

			$songs = [];
			foreach ($songbookItem->songs as $song) {
				$songItem = new SongItem($this->database);
				$songItem->getSongById($song->id);
				$songs[] = $songItem;
			}

			$this->template->songs = $songs;
			$this->template->songbook = $songbookItem;
			$this->template->username = $username;

			$this->setView($view);

		} else {
			$this->flashMessage('Uživatel ' . $username . ' neexistuje.', 'error');
			$this->redirect('Homepage:default');
		}
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

		return $this->songbookFactory->create(function ($username, $guid) {
			$this->flashMessage('Zpěvník byl uložen', 'success');
			$this->redirect('Song:detail', ['user' => $username, 'guid' => $guid]);
		}, $user);
	}

	/**
	 * Songbook edit form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongbookEditForm()
	{
		// dump($this->songbookItem); exit;
		return $this->songbookEditFactory->create(function ($username, $guid) {
			$this->redirect('Songbook:edit', [$username, $guid]);
		}, $this->songbookItem);
	}

}
