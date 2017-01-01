<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model;


class SongbookFormFactory
{
	use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var Model\SongbookManager */
	private $songbookManager;


	public function __construct(FormFactory $factory, Model\SongbookManager $songbookManager)
	{
		$this->factory = $factory;
		$this->songbookManager = $songbookManager;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess, $user)
	{
		$form = $this->factory->create();

		$form->addHidden('user', $user);

		$form->addText('title', 'Název')
			->setAttribute('placeholder', 'Název zpěvníku')
			->setAttribute('autocomplete', 'off')
			->setAttribute('autofocus')
			->setRequired('Prosím, zadej název zpěvníku.');

		$form->addText('guid', 'Název v URL')
			->setAttribute('placeholder', 'název-v-url')
			->setAttribute('autocomplete', 'off')
		     ->setRequired('Prosím, zadej zázev pro URL.');

		$form->addCheckbox('default', 'Hlavní zpěvník');

		$form->addSubmit('send', 'Uložit zpěvník');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			$songbook = $this->songbookManager->add($values->user, $values->title, $values->guid, $values->default );
			$onSuccess($songbook->guid);
		};

		return $form;
	}

}
