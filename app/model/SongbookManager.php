<?php

namespace App\Model;

use Nette;


/**
 * Songsbook management.
 */
class SongbookManager extends Nette\Object
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

	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Adds new songbook.
	 * @param  string Users ID
	 * @param  string Songbook title
	 * @param  string Songbook guid
	 * @return Songbook
	 * @throws DuplicateNameException
	 */
	public function add($user_id, $title, $guid )
	{
		try {
			$songbook = $this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_USER => $user_id,
				self::COLUMN_TITLE => $title,
				self::COLUMN_GUID => $guid,
			]);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
		return $songbook;
	}

	private function guidExist($guid)
	{

	}

	/**
	 * Get list of all songbooks of current user.
	 * @param  string Users ID
	 * @return array list of songbooks
	 */
	public function getUsersSongbooks($user)
	{
		return $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_USER, $user);
	}

	/**
	 * Get list of all songbooks of song.
	 * @param  string Song ID
	 * @return array list of song ids.
	 */
	public function getSongsSongbooks($song)
	{
		return $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_USER, $song);
	}


}
