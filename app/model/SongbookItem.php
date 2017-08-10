<?php

namespace App\Model;

use Nette;
use App\Model\UserManager;


/**
 * songbook item.
 */
class SongbookItem extends Nette\Object
{
	use Nette\SmartObject;

	const
		TABLE_NAME = 'zabe_songbooks',
		COLUMN_ID = 'id',
		COLUMN_USER = 'zabe_users_id',
		COLUMN_ORDER = 'order',
		COLUMN_DEFAULT = 'default',
		COLUMN_TITLE = 'title',
		COLUMN_GUID = 'guid',
		COLUMN_STYLESHEET = 'stylesheet',
		COLUMN_FONTS = 'fonts';

	const
		RELATION_TABLE_NAME = 'zabe_song_songbook_relations',
		RELATION_SONGBOOK = 'zabe_songbooks_id',
		RELATION_SONG = 'zabe_songs_id',
		RELATION_ORDER = 'order';

	const
		SONGBOOK_OTHERS_ID = -2,
		SONGBOOK_OTHERS_TITLE = 'Nezařazené',
		SONGBOOK_OTHERS_GUID = 'nezarazene',
		SONGBOOK_OTHERS_DEFAULT = 0,
		SONGBOOK_OTHERS_ORDER = 9999;

	const
		SONGBOOK_ALL_ID = -1,
		SONGBOOK_ALL_TITLE = 'Všechny písně',
		SONGBOOK_ALL_GUID = 'vse',
		SONGBOOK_ALL_DEFAULT = 0,
		SONGBOOK_ALL_ORDER = 9998;

	/** @var Nette\Database\Context */
	private $database;

	public $id;
	public $user;
	public $username;
	public $title;
	public $guid;
	public $default;
	public $order;
	public $songs;
	public $stylesheet;
	public $fonts;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Get songbook data.
	 * @param  string  songbook id or guid
	 * @return bool true on success
	 */
	public function getSongbook($username, $songbook = '')
	{

		$userManager = new UserManager($this->database);
		$userID = $userManager->getIDByNick($username);
		$this->username = $username;

		if ($songbook == self::SONGBOOK_OTHERS_GUID) {

			$this->id = self::SONGBOOK_OTHERS_ID;
			$this->user = $userID;
			$this->title = self::SONGBOOK_OTHERS_TITLE;
			$this->guid = self::SONGBOOK_OTHERS_GUID;
			$this->default = 0;
			$this->order = 10000;
			$this->stylesheet = null;
			$this->fonts = null;

			return true;

		} else if ($songbookItem = $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_GUID, $songbook)->where(self::COLUMN_USER, $userID)->fetch()) {

			$this->id = $songbookItem->id;
			$this->user = $songbookItem->user;
			$this->title = $songbookItem->title;
			$this->guid = $songbookItem->guid;
			$this->default = $songbookItem->default;
			$this->order = $songbookItem->order;
			$this->stylesheet = $songbookItem->stylesheet;
			$this->fonts = $songbookItem->fonts;

			return true;

		} else {
			$this->id = self::SONGBOOK_ALL_ID;
			$this->user = $userID;
			$this->title = self::SONGBOOK_ALL_TITLE;
			$this->guid = self::SONGBOOK_ALL_GUID;
			$this->default = self::SONGBOOK_ALL_DEFAULT;
			$this->order = self::SONGBOOK_ALL_ORDER;
			$this->stylesheet = null;
			$this->fonts = null;

			return true;
		}

		return false;
	}

	/**
	 * Get songs of songbooks
	 * @return array of songItems
	 */
	public function getSongs()
	{

		$songManager = new SongManager($this->database);

		if ($this->id == -2) {
			// load others songs
			$this->songs = $songManager->getOthersSongs($this->user);
		} else if ($this->id == -1) {
			// load all songs
			$this->songs = $songManager->getAllUsersSongs($this->user);
		} else {
			// load songs by songbook id
			$relations = $this->database->table(self::RELATION_TABLE_NAME)->order(self::RELATION_ORDER)->select(self::RELATION_SONG)->where(self::RELATION_SONGBOOK, $this->id)->fetchAll();

			$songs = [];
			foreach ($relations as $relation) {
				$relation;
				$songs[] = $relation->songs;
			}
			$this->songs = $songs;
		}
	}

}
