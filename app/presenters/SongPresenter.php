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

}
