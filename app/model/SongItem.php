<?php

namespace App\Model;

use Nette;


/**
 * Songs item.
 */
class SongItem extends Nette\Object
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

	public $id;
	public $user;
	public $title;
	public $guid;
	public $interpreter;
	public $lyric;
	public $lyricSource;
	public $songbooks;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Get song data.
	 * @param  string  Song id or guid
	 * @return string HTML text
	 */
	public function getSong($user, $id)
	{

		$userManager = new UserManager($this->database);
		$userID = $userManager->getIDByNick($user);


		$song = $this->database->table(self::TABLE_NAME )
			->select('*')
			->where(self::COLUMN_GUID, $id)
			->where(self::COLUMN_USER, $userID)
			->fetch();

		if ($song) {

			$this->id = $song->id;
			$this->user = $song->user;
			$this->title = $song->title;
			$this->guid = $song->guid;
			$this->interpreter = $song->interpreter;
			$this->lyric = $this->markupParser($song->lyric);
			$this->lyricSource= $song->lyric;
			$this->songbooks = $this->getUsedSongbooksIDs();

			return true;

		} else {
			return false;
		}

	}

	/**
	 * Get list of used songbooks IDs.
	 * @return array list of songbook ids.
	 */
	protected function getUsedSongbooksIDs()
	{
		$songbooks = $this->database->table(self::RELATION_TABLE_NAME )->select('*')->where(self::RELATION_SONG, $this->id)->fetchAll();
		foreach ($songbooks as $songbook) {
			$ids[] = $songbook->zabe_songbooks_id;
		}
		if (!empty($ids)) {
			return $ids;
		} else {
			return false;
		}
	}

	/**
	 * Markup parser.
	 * @param  string Markup text
	 * @return string HTML text
	 */
	protected function markupParser($text) {

		$verseTag = '<span class="chord-verse"></span>';
		$chorusTag = '<span class="chord-chorus">R:</span>';

		$pattern[] = '/<[v|s]>/';
		$pattern[] = '/<(ch|r)>/';
		$pattern[] = '/<([a-zA-Z1-9]*)>/';
		$replacement[] = $verseTag;
		$replacement[] = $chorusTag;
		$replacement[] = '<span class="chord">$1</span>';
		$markuped = nl2br(preg_replace($pattern, $replacement, $text));

		$line_list = explode('<br />', $markuped);

		$i = 0;
		$verse = 0;
		foreach ($line_list as $line) {

			$line_list[$i] = str_replace('<br />', '', $line_list[$i]);

			if (strpos($line_list[$i], '<span class="chord">')) {
				$line_list[$i] = '<p class="chord-line">' . $line_list[$i] . '</p>';
			} elseif (!preg_match('/^<\/div>$/', trim($line_list[$i]))&&!preg_match('/^<div class=\"(verse|chorus)\">$/', trim($line_list[$i]))) {
				$line_list[$i] .= '<br>';
			}

			if (strpos($line_list[$i], $verseTag)) {
				$line_list[$i] = str_replace($verseTag, '<span class="chord-verse">' . ++$verse . '.</span>', $line_list[$i]);
			}

			if (strpos($line_list[$i], $chorusTag)) {
				if (trim($line_list[$i]) !== $chorusTag . '<br>') {
					$chorusLine = $line_list[$i];
				} else {
					if (!empty($chorusLine)) {
						$line_list[$i] = preg_replace('/<span class="chord">([a-zA-Z1-9]*)<\/span>/', '', $chorusLine);
						$line_list[$i] = $chorusTag . substr(trim(strip_tags($line_list[$i])), 2) . '&hellip;<br>';
					}
				}
			}

			$i++;
		}

		$lyric = implode($line_list);

		return $lyric;
	}

}
