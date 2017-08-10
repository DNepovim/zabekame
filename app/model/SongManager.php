<?php

namespace App\Model;

use Nette;
use Nette\Utils\Strings;
use DOMDocument;
use DOMXPath;
use App\Model\UserManager;


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

	const
		RELATION_TABLE_NAME = 'zabe_song_songbook_relations',
		RELATION_SONGBOOK = 'zabe_songbooks_id',
		RELATION_SONG = 'zabe_songs_id',
		REALTION_ORDER = 'order';

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
	public function add($userID, $title, $guid, $interpreter, $lyric, $songbooks = null)
	{
		try {
			$song = $this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_USER => $userID,
				self::COLUMN_TITLE => $title,
				self::COLUMN_GUID => $guid,
				self::COLUMN_INTERPRETER => $interpreter,
				self::COLUMN_LYRIC => $lyric,
			]);

			if (!empty($songbooks)) {
				foreach ($songbooks as $songbook) {
					$songRelation = $this->database->table(self::RELATION_TABLE_NAME)->insert([
						self::RELATION_SONGBOOK => $songbook,
						self::RELATION_SONG=> $song,
					]);
				}
			}
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
		return $song;
	}

	/**
	 * Edit existing song
	 * @title  string
	 * @guid  string
	 * @interpreter  string
	 * @lyric  string
	 * @return void
	 * @throws DuplicateNameException
	 */
	public function edit($id, $title, $guid, $interpreter, $lyric, $songbooks)
	{
		try {
			$this->database->table(self::TABLE_NAME)->get($id)->update([
				self::COLUMN_TITLE => $title,
				self::COLUMN_GUID => $guid,
				self::COLUMN_INTERPRETER => $interpreter,
				self::COLUMN_LYRIC => $lyric,
			]);

			$this->database->table(self::RELATION_TABLE_NAME)->select('*')->where(self::RELATION_SONG, $id)->delete();

			foreach ($songbooks as $songbook) {
				$this->database->table(self::RELATION_TABLE_NAME)->insert([
					self::RELATION_SONGBOOK => $songbook,
					self::RELATION_SONG=> $id,
				]);
			}

		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

	public function remove($username, $guid)
	{

		$userManager = new UserManager($this->database);
		$userID = $userManager->getIDByNick($username);
		
		if ($songID = $this->getIdByGuid($guid)) {

			$this->database->table(self::RELATION_TABLE_NAME)->where(self::RELATION_SONG, $songID)->delete();
			return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_USER, $userID)->where(self::COLUMN_GUID, $guid)->delete();

		} else {

			return false;

		}

	}


	public function import( $user, $url, $source)
	{
		$data = $this->importFromSM($url);

		$guid = Strings::webalize($data['title']);

		$songManager = new SongManager($this->database);
		$song = $songManager->add($user, $data['title'], $guid, $data['interpreter'], $data['lyric']);

		return $song;
	}

	private function importFromSM($url)
	{

		$dokument = new DOMDocument();

		@$dokument->loadHTMLFile($url);

		$xpath = new \DOMXPath($dokument);

		$pageTitle = $xpath->query('//title')->item(0)->nodeValue;
		$pageTitle = explode(' - ', $pageTitle);

		$title = preg_replace('/ \[.*/', '', $pageTitle[1]);
		$interpreter = $pageTitle[0];

		$content = $xpath->query('//td[@class="piesen"]/font')->item(0)->C14N();

		$pattern[] = '/<script(.*?)\/script>/s';
		$pattern[] = '/<font(.*?)>/';
		$pattern[] = '/<(.?)sup>/';
		$pattern[] = '/<\/font>.*/';
		$pattern[] = '/&#xD;/';
		$pattern[] = '/<br>(.*?)<\/br>/';

		$replacement[] = '';
		$replacement[] = '';
		$replacement[] = '';
		$replacement[] = '';
		$replacement[] = '';
		$replacement[] = "\n";

		$filteredContent = preg_replace($pattern, $replacement, $content);

		$pattern[] = '/<a(.*?)>(.*?)<\/a>/';

		$markupedContent = preg_replace_callback('/<a(.*?)>(.*?)<\/a>/',
			function ($matches){
				return '<'. ucfirst($matches[2]) . '>';
			},
			$filteredContent);

		$data = array(
			'title' => $title,
			'interpreter' => $interpreter,
			'lyric' => $markupedContent
		);

		return $data;

	}

	public function getListOfUserGuids($user)
	{
		return $this->database->table(self::TABLE_NAME )->where(self::COLUMN_USER, $user)->fetchPairs(self::COLUMN_ID, self::COLUMN_GUID);
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
	public function getSongsById($ids)
	{
		return $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_ID, $ids)->fetchAll();
	}

	/**
	 * Get guid by id
	 * @songbook string Song ID
	 * @return array of songs
	 */
	public function getGuidById($songID)
	{
		return $this->database->table(self::TABLE_NAME )->select(self::COLUMN_GUID)->get($songID)->guid;
	}

	/**
	 * Get id by guid
	 * @songbook string Song ID
	 * @return array of songs
	 */
	public function getIdByGuid($guid)
	{
		$result = $this->database->table(self::TABLE_NAME )->select(self::COLUMN_ID)->where(self::COLUMN_GUID, $guid)->fetch();
		if ($result) {
			return $result->id;
		} else {
			return false;
		}
	}

	/**
	 * Get list of song with no songbook relations
	 * @param integer Songbook ID
	 * @return array of song IDs
	 */
	public function getOthersSongs($user)
	{

		$songs = $this->database->table(self::TABLE_NAME )
			->select(self::COLUMN_ID)
			->select(self::COLUMN_TITLE)
			->select(self::COLUMN_GUID)
			->select(self::COLUMN_INTERPRETER)
			->where(self::COLUMN_USER, $user)->fetchAll();

		foreach ($songs as $song) {
			$songsID[] = $song->id;
		}

		$relations = $this->database->table(self::RELATION_TABLE_NAME)->select(self::RELATION_SONG)->where(self::RELATION_SONG, $songs)->fetchAll();

		foreach ($relations as $relation) {
			unset($songs[$relation->zabe_songs_id]);
		}

		return $songs;
	}

	/**
	 * Get list of all users songs
	 * @param integer Songbook ID
	 * @return array of song IDs
	 */
	public function getAllUsersSongs($userID)
	{
		$songs = $this->database->table(self::TABLE_NAME )
								->select(self::COLUMN_ID)
								->select(self::COLUMN_TITLE)
								->select(self::COLUMN_GUID)
								->select(self::COLUMN_INTERPRETER)
								->where(self::COLUMN_USER, $userID)->fetchAll();

		return $songs;

	}
}
