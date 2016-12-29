<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use App\Model;


class SignUpFormFactory
{
	use Nette\SmartObject;

	const PASSWORD_MIN_LENGTH = 7;

	/** @var FormFactory */
	private $factory;

	/** @var Model\UserManager */
	private $userManager;


	public function __construct(FormFactory $factory, Model\UserManager $userManager)
	{
		$this->factory = $factory;
		$this->userManager = $userManager;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess)
	{
		$form = $this->factory->create();
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Prosím, zadej uživatelské jméno.')
			->setAttribute('class', 'form-input form-text');

		$form->addText('firstname', 'Křestní jméno:')
			->setAttribute('class', 'form-input form-text');

		$form->addText('surname', 'Příjmení:')
			->setAttribute('class', 'form-input form-text');

		$form->addEmail('email', 'E-mail:')
			->setRequired('Prosím zadejte svůj e-mail.')
			->setAttribute('class', 'form-input form-text');

		$form->addPassword('password', 'Heslo:')
			->setOption('description', sprintf('alespoň %d znaků', self::PASSWORD_MIN_LENGTH))
			->setRequired('Heslo.')
			->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH)
			->setAttribute('class', 'form-input form-text form-pass');

		$form->addSubmit('send', 'Vytvořit účet');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			try {
				$this->userManager->add($values->username, $values->firstname, $values->surname, $values->email, $values->password);
			} catch (Model\DuplicateNameException $e) {
				$form['username']->addError('Uživatelské jméno již existuje. Použijte jiné.');
				return;
			}
			$onSuccess();
		};

		return $form;
	}

}
