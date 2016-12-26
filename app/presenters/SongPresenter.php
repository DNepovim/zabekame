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

	/** @var Forms\SongEditFormFactory @inject */
	public $songEditFactory;

	public $id;

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
		$songManager = new SongManager($this->database);
		$this->template->songs = $songs = $songManager->getUsersSongs($this->getUser()->id);
	}

	/**
	 * Render detail
	 */
	public function renderDetail( $id )
	{
		$songItem = new SongItem($this->database);
		$songItem->getSong($id);
		$this->template->song = $songItem;
	}

	/**
	 * Render edit
	 */
	public function renderEdit( $id )
	{
		$this->id = $id;
	}
	/**
	 * Song form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongForm()
	{
		$user = $this->getUser()->id;

		return $this->songFactory->create(function ($guid) {
			$this->redirect('Song:detail', $guid);
		}, $user);
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

		return $this->songEditFactory->create(function () {
		}, $song);
	}
}
