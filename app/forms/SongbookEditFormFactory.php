<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model;


class SongbookEditFormFactory
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
	public function create(callable $onSuccess, $songbook)
	{
		$form = $this->factory->create();

		$form->addHidden('user', $songbook->user['id']);

		$form->addText('title', 'Název')
			->setAttribute('placeholder', 'Název zpěvníku')
			->setAttribute('autocomplete', 'off')
			->setAttribute('autofocus')
			->setDefaultValue($songbook->title)
			->setRequired('Prosím, zadej název zpěvníku.');

		$form->addText('guid', 'Název v URL')
			->setAttribute('placeholder', 'název-v-url')
			->setAttribute('autocomplete', 'off')
			->setDefaultValue($songbook->guid)
		     ->setRequired('Prosím, zadej zázev pro URL.');

		$form->addCheckbox('default', 'Hlavní zpěvník')
			->setDefaultValue($songbook->default);

		$form->addSubmit('send', 'Uložit zpěvník');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			$songbook = $this->songbookManager->edit($values->user, $values->title, $values->guid, $values->default );
			$onSuccess($values->guid);
		};

		return $form;
	}

}
