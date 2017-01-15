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
	public function create(callable $onSuccess, $userID, $username, $songbooks)
	{
		$form = $this->factory->create();

		$form->addHidden('userID', $userID);
		$form->addHidden('username', $username);

		$form->addText('title', 'Název')
			->setAttribute('placeholder', 'Název')
			->setAttribute('autocomplete', 'off')
			->setAttribute('autofocus')
			->setRequired('Prosím, zadej název písně.');


		$form->addText('interpreter', 'Interpret')
			->setAttribute('placeholder', 'Interpret')
			->setAttribute('autocomplete', 'off');

		$form->addText('guid', 'Název v URL')
			->setAttribute('placeholder', 'název-v-url')
			->setAttribute('autocomplete', 'off')
			->setAttribute('tabindex', '-1')
			->setRequired('Prosím, zadej zázev pro URL.');

		foreach ($songbooks as $item) {
			$options[$item->id] = $item->title;
		}

		$form->addCheckboxList('songbooks', 'Zpěvník', $options );

		$form->addTextarea('lyric', 'Text:')
			->setAttribute('placeholder', 'Text písně')
			->setRequired('Prosím, vlož text.');

		$form->addSubmit('send', '(uložit)');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			$song = $this->songManager->add($values->userID, $values->title, $values->guid, $values->interpreter, $values->lyric, $values->songbooks );
			$onSuccess($values->username, $song->guid);
		};

		return $form;
	}

}
