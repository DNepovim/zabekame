<?php

namespace App\Model;

use Nette;


/**
 * Songs management.
 */
class SongManager extends Nette\Object
{
	use Nette\SmartObject;

	const
		TABLE_NAME = 'zabe_songs',
		COLUMN_ID = 'id',
		COLUMN_USER = 'zabe_users_id',
		COLUMN_TITLE = 'title',
		COLUMN_GUID = 'guid',
		COLUMN_INTERPRETER = 'interpreter',
		COLUMN_LYRIC = 'lyric';

	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Adds new song.
	 * @param  string
	 * @param  string
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return void
	 * @throws DuplicateNameException
	 */
	public function add($user_id, $title, $guid, $interpreter, $lyric)
	{
		try {
			$song = $this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_USER => $user_id,
				self::COLUMN_TITLE => $title,
				self::COLUMN_GUID => $guid,
				self::COLUMN_INTERPRETER => $interpreter,
				self::COLUMN_LYRIC => $lyric,
			]);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
		return $song;
	}

	private function guidExist($guid)
	{

	}

	/**
	 * Get list of all songs of current user.
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return array list of songs
	 */
	public function getUsersSongs($user)
	{
		return $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_USER, $user);
	}

	/**
	 * Get list of songs by id
	 * @songbook string Songbook ID
	 * @return array of songs
	 */
	public function getSongsByIds($ids)
	{
		return $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_ID, $ids)->fetchAll();
	}


}
