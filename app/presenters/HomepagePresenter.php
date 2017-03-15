<?php

namespace App\Presenters;

use Nette;
use App\Model;


class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect('User:dashboard');
		} else {
			$this->redirect('Sign:in');
		}
	}

}
