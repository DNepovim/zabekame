<?php

namespace App\Presenters;

use Nette;
use App\Model\SongManager;
use App\Model\SongbookManager;
use App\Forms;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var Forms\SignInFormFactory @inject */
	public $signInFactory;

	/** @var Forms\SignUpFormFactory @inject */
	public $signUpFactory;

	/** @var Nette\Database\Context */
	public $database;
	
	/** @persistent */
	public $backlink = '';

	public function __construct( Nette\Database\Context $database ) {
		$this->database = $database;
	}

	public function beforeRender() {
		parent::beforeRender();

		$this->template->snippet = $this->context->parameters['mango'];

		$user = $this->getUser();

		if ($user->isLoggedIn()) {
			$this->template->currentUser = $user->identity->getRoles();
		}

	}

	/**
	 * Get list of related guids
	 * @return array
	 */
	protected function getGuids()
	{

		$userID = $this->getUser()->id;

		$songManager = new SongManager($this->database);
		$songbookManager = new SongbookManager($this->database);

		$guids = array_merge($songManager->getListOfUserGuids($userID), $songbookManager->getListOfUserGuids($userID));

		return $guids;
	}

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		return $this->signInFactory->create(function () {
			$this->restoreRequest($this->backlink);
			$this->redirect('User:dashboard');
		});
	}


	/**
	 * Sign-up form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignUpForm()
	{
		return $this->signUpFactory->create(function () {
			$this->redirect('Homepage:');
		});
	}


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->redirect('Sign:in');
	}
}
