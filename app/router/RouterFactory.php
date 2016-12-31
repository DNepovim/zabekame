<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;

		$router[] = new Route('dashboard', 'User:dashboard');
		$router[] = new Route('<user>/others', 'Songbook:others');
		$router[] = new Route('song/add', 'Song:add');
		$router[] = new Route('song/import', 'Song:import');
		$router[] = new Route('song[/<id>]', 'Song:detail');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Sign:in');

		return $router;
	}

}
