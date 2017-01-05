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
	public function create(callable $onSuccess, $song, $songbooksList)
	{
		$form = $this->factory->create();

		$form->addHidden('id', $song->id);

		$form->addText('title', 'Název')
			->setDefaultValue($song->title)
			->setAttribute('placeholder', 'Název')
			->setAttribute('autocomplete', 'off')
			->setAttribute('autofocus')
			->setRequired('Prosím, zadej název písně.');


		$form->addText('interpreter', 'Interpret')
			->setAttribute('placeholder', 'Interpret')
			->setAttribute('autocomplete', 'off')
			->setDefaultValue($song->interpreter);

		$form->addText('guid', 'Název v URL')
			->setDefaultValue($song->guid)
			->setAttribute('placeholder', 'název-v-url')
			->setAttribute('autocomplete', 'off')
			->setAttribute('tabindex', '-1')
	        ->setRequired('Prosím, zadej zázev pro URL.');

		if (!empty($songbooksList)) {
			foreach ($songbooksList as $item) {
				$options[$item->id] = $item->title;
			}

			if (!empty($song->songbooks)) {
				$form->addCheckboxList('songbooks', 'Zpěvník', $options )
				     ->setDefaultValue($song->songbooks);
			} else {
				$form->addCheckboxList('songbooks', 'Zpěvník', $options );
			}
		}




		$form->addTextarea('lyric', 'Text:')
			->setDefaultValue($song->lyricSource)
			->setAttribute('placeholder', 'Text písně')
			->setRequired('Prosím, vlož text.');

		$form->addSubmit('send', '(uložit)');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			$this->songManager->edit( $values->id, $values->title, $values->guid, $values->interpreter, $values->lyric, $values->songbooks );
			$onSuccess($values->guid);
		};

		return $form;
	}

}
