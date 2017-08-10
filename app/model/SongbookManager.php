<?php

namespace App\Model;

use Nette;
use App\Model\UserManager;


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
		
	const
		RELATION_TABLE_NAME = 'zabe_song_songbook_relations',
		RELATION_SONGBOOK = 'zabe_songbooks_id',
		RELATION_SONG = 'zabe_songs_id',
		RELATION_ORDER = 'order';

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
	public function add($user_id, $title, $guid, $default )
	{
		try {
			if ($default) {
				$songbooks = $this->getUsersSongbooks($user_id);
				foreach ($songbooks as $songbook) {
					$songbooksID[] = $songbook->id;
				}
				$this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $songbooksID)->update( [self::COLUMN_DEFAULT => 0] );
			}

			$songbook = $this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_USER => $user_id,
				self::COLUMN_TITLE => $title,
				self::COLUMN_GUID => $guid,
				self::COLUMN_DEFAULT=> $default,
			]);

		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
		return $songbook;
	}

	/**
	 * Edit songbook.
	 * @param  string Songbook title
	 * @param  string Songbook guid
	 * @return Songbook
	 * @throws DuplicateNameException
	 */
	public function edit($values)
	{
		try {
			// if is default options checket, remove all default values and set new
			if ($values->default) {
				$songbookItem = new SongbookItem($this->database);
				$songbookItem->getSongbook($values->username, $values->originGuid);

				$songbooks = $this->getUsersSongbooks($songbookItem->user->id);
				foreach ($songbooks as $songbook) {
					$songbooksID[] = $songbook->id;
				}
				$this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $songbooksID)->update( [self::COLUMN_DEFAULT => 0] );
			}

			$userManager = new userManager($this->database);
			$userID = $userManager->getIDByNick($values->username);

			$songbook = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_USER, $userID)->where(self::COLUMN_GUID, $values->originGuid)->update([
				self::COLUMN_TITLE => $values->title,
				self::COLUMN_GUID => $values->guid,
				self::COLUMN_DEFAULT => $values->default,
			]);

			if (!empty($values->order)) {
				$order = json_decode($values->order);
				foreach ($order as $id => $or) {
					$songbook = $this->database->table(self::RELATION_TABLE_NAME)->where(self::RELATION_SONGBOOK, $songbookItem->id)->where(self::RELATION_SONG, $id)->update([self::RELATION_ORDER => $or]);
				}
			}

		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
		return $songbook;
	}

	/**
	 * Get list of all songbooks of current user.
	 * @param  string Users ID
	 * @return array list of songbooks
	 */
	public function getUsersSongbooks($user)
	{
		return $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_USER, $user)->order(self::COLUMN_ORDER)->fetchAll();
	}

	/**
	 * Get list of user's guids.
	 * @param  string Users ID
	 * @return array list of guids
	 */
	public function getListOfUserGuids($user)
	{
		return $this->database->table(self::TABLE_NAME )->where(self::COLUMN_USER, $user)->fetchPairs(self::COLUMN_ID, self::COLUMN_GUID);
	}

	/**
	 * Get default user's default songbook.
	 * @param  string Username
	 * @return guid
	 */
	public function getDefaultSongbook($username)
	{
		return $this->database->table(self::TABLE_NAME )->where('zabe_users.username', $username)->where(self::COLUMN_DEFAULT, 1)->fetchPairs(self::COLUMN_ID, self::COLUMN_GUID);
	}
}
