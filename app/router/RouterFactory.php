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

		$router[] = new Route('sign/<action>', 'Sign:');
		$router[] = new Route('song/import', 'Song:import');
		$router[] = new Route('<username>/<songguid>/edit', 'Song:edit');
		$router[] = new Route('<username>/<songguid>/odstranit', 'Song:remove');
		$router[] = new Route('<username>/pridatzpevnik', 'Songbook:add');
		$router[] = new Route('<username>/pridatpisen', 'Song:add');
		$router[] = new Route('<username>[/<songbook>]/<songguid>', 'Song:detail');
		$router[] = new Route('<username>[/<action>]', 'User:dashboard');
//		$router[] = new Route('<user>/<songbook>/<song>', 'Song:detail');
//		$router[] = new Route('song/add', 'Song:add');
//		$router[] = new Route('song/import', 'Song:import');
//		$router[] = new Route('song[/<id>]', 'Song:detail');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Sign:in');

		return $router;
	}

}
