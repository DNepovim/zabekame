<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model;


class SongFormFactory
{
	use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var Model\SongManager */
	private $songManager;


	public function __construct(FormFactory $factory, Model\SongManager $songManager)
	{
		$this->factory = $factory;
		$this->songManager = $songManager;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess, $user)
	{
		$form = $this->factory->create();

		$form->addHidden('user', $user);

		$form->addText('title', 'Název')
			->setRequired('Prosím, zadej název písně.');


		$form->addText('interpreter', 'Interpret');

		$form->addText('guid', 'Název v URL')
		     ->setRequired('Prosím, zadej zázev pro URL.');

		$form->addTextarea('lyric', 'Text:')
			->setRequired('Prosím, vlož text.');

		$form->addSubmit('send', 'Uložit píseň');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			$song = $this->songManager->add($values->user, $values->title, $values->guid, $values->interpreter, $values->lyric );
			$onSuccess($song->guid);
		};

		return $form;
	}

}
