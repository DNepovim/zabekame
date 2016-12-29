<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model;


class SongImportFormFactory
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

		$form->addText('url', 'URL')
			->setRequired('Prosím, zadej název url.');

		$options['sm'] = 'Supermusic.sk';

		$form->addRadioList('source', 'Zdroj', $options);

		$form->addSubmit('send', 'Importovat');


		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			$song = $this->songManager->import($values->user, $values->url, $values->source );
			$onSuccess($song->guid);
		};

		return $form;
	}

}
