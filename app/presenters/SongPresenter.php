<?php

namespace App\Presenters;

use Nette;
use App\Forms;


class SongPresenter extends BasePresenter
{
	/** @var Forms\SongFormFactory @inject */
	public $songFactory;

	/**
	 * Song form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSongForm()
	{
		return $this->songFactory->create(function () {
			$this->redirect('Homepage:');
		});
	}
}
