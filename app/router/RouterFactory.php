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

		$router[] = new Route('', 'Homepage:default');
		$router[] = new Route('/nastenka', 'User:dashboard');
		$router[] = new Route('sign/<action>', 'Sign:');
		$router[] = new Route('song/import', 'Song:import');
		$router[] = new Route('<username>/pridatzpevnik', 'Songbook:add');
		$router[] = new Route('<username>/pridatpisen', 'Song:add');
		$router[] = new Route('<username>/<guid>/odstranit', 'Song:remove');
		$router[] = new Route('<username>/<songbook>/odstranit', 'Songbook:remove');
		$router[] = new Route('<username>/<guid>/upravit', 'Song:edit');
		$router[] = new Route('<username>/<guid>/upravitzpevnik', 'Songbook:edit');
		$router[] = new Route('<username>[/<songbook>]/<guid>', 'Song:detail');
		$router[] = new Route('<username>[/<songbook>]', 'Songbook:detail');
		$router[] = new Route('<presenter>/<action>[/<id>]');

		return $router;
	}

}
