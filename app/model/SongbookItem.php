<?php

namespace App\Model;

use Nette;


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
		COLUMN_GUID = 'guid';

	const
		RELATION_TABLE_NAME = 'zabe_song_songbook_relations',
		RELATION_SONGBOOK = 'zabe_songbooks_id',
		RELATION_SONG = 'zabe_songs_id',
		REALTION_ORDER = 'order';

	/** @var Nette\Database\Context */
	private $database;

	public $id;
	public $user;
	public $title;
	public $guid;
	public $order;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Get songbook data.
	 * @param  string  songbook id or guid
	 * @return bool true on success
	 */
	public function getSongbook($id)
	{
		if (is_numeric($id)) {
			$songbook = $this->database->table( self::TABLE_NAME )->select('*')->get( $id );
		} else {
			$songbook = $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_GUID, $id)->fetch();
		}

		if ($songbook) {

			$this->id = $songbook->id;
			$this->user = $songbook->user;
			$this->title = $songbook->title;
			$this->guid = $songbook->guid;
			$this->order = $songbook->order;

			return true;

		} else {
			return false;
		}
	}

	/**
	 * Get list of song IDs related to songbook
	 * @param integer Songbook ID
	 * @return array of song IDs
	 */
	public function getSongsIDFromSongbook($songbook)
	{

		if (is_numeric($songbook)) {
			$relations = $this->database->table(self::RELATION_TABLE_NAME)->select(self::RELATION_SONG)->where(self::RELATION_SONGBOOK, $songbook)->fetchAll();
		} else {
			$songbook = $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_GUID, $songbook)->fetch();
			$relations = $this->database->table(self::RELATION_TABLE_NAME)->select(self::RELATION_SONG)->where(self::RELATION_SONGBOOK, $songbook->id)->fetchAll();
		}


		foreach ($relations as $relation) {
			$ids[] = $relation->zabe_songs_id;
		}
		return $ids;
	}
}
