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
		$router[] = new Route('<username>', 'Songbook:detail');
		$router[] = new Route('sign/<action>', 'Sign:');
		$router[] = new Route('song/import', 'Song:import');
		$router[] = new Route('songbook/export', 'Songbook:export');
		$router[] = new Route('<username>/zpevnik[/<songbook>]', 'Songbook:detail');
		$router[] = new Route('<username>/zpevnik/<songbook>/upravit', 'Songbook:edit');
		$router[] = new Route('<username>/<guid>/upravit', 'Song:edit');
		$router[] = new Route('<username>/<guid>/odstranit', 'Song:remove');
		$router[] = new Route('<username>/pridatzpevnik', 'Songbook:add');
		$router[] = new Route('<username>/pridatpisen', 'Song:add');
		$router[] = new Route('<username>[/<songbook>]/<songguid>', 'Song:detail');
		$router[] = new Route('<username>', 'User:dashboard');
//		$router[] = new Route('<user>/<songbook>/<song>', 'Song:detail');
//		$router[] = new Route('song/add', 'Song:add');
//		$router[] = new Route('song/import', 'Song:import');
//		$router[] = new Route('song[/<id>]', 'Song:detail');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Sign:in');

		return $router;
	}

}
