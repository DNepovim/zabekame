<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model;


class SongEditFormFactory
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
	public function create(callable $onSuccess, $song)
	{

		$form = $this->factory->create();

		$form->addHidden('id', $song->id);

		$form->addText('title', 'Název')
			->setDefaultValue($song->title)
			->setRequired('Prosím, zadej název písně.');


		$form->addText('interpreter', 'Interpret')
			->setDefaultValue($song->interpreter);

		$form->addText('guid', 'Název v URL')
			->setDefaultValue($song->guid)
		     ->setRequired('Prosím, zadej zázev pro URL.');

		$form->addTextarea('lyric', 'Text:')
			->setDefaultValue($song->lyric)
			->setRequired('Prosím, vlož text.');

		$form->addSubmit('send', 'Uložit píseň');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			$this->songManager->edit( $values->id, $values->title, $values->guid, $values->interpreter, $values->lyric );
			$onSuccess();
		};

		return $form;
	}

}
