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
	public $userID;
	public $username;
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
	 * @param  string  Song guid
	 * @param  string  Username
	 * @return string bool
	 */
	public function getSong($username, $songGuid)
	{

		$userManager = new UserManager($this->database);
		$userID = $userManager->getIDByNick($username);


		$song = $this->database->table(self::TABLE_NAME )
			->select('*')
			->where(self::COLUMN_GUID, $songGuid)
			->where(self::COLUMN_USER, $userID)
			->fetch();

		if ($song) {

			$this->id = $song->id;
			$this->userID = $song->user->id;
			$this->username = $username;
			$this->title = $song->title;
			$this->guid = $song->guid;
			$this->interpreter = $song->interpreter;
			$this->lyric = $song->lyric;
			$this->songbooks = $this->getUsedSongbooksIDs();

			return true;

		} else {
			return false;
		}

	}

	/**
	 * Get song data by id.
	 * @param int Song id
	 * @return bool
	 */

	public function getSongById($id)
	{

		$song = $this->database->table(self::TABLE_NAME )
			->select('*')
			->where(self::COLUMN_ID, $id)
			->fetch();


		if ($song) {

			$userManager = new UserManager($this->database);
			$username = $userManager->getNickByID($song->user->id);

			$this->id = $song->id;
			$this->userID = $song->user->id;
			$this->username = $username;
			$this->title = $song->title;
			$this->guid = $song->guid;
			$this->interpreter = $song->interpreter;
			$this->lyric = $song->lyric;
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
	 * Parse markup 
	 * @param string Markup text
	 * @param array Parser options
	 * @return string HTML text
	 */
	protected function markupParser($text, $options) 
	{
		
		$options['verseOpenTag'] = isset($options['verseOpenTag']) ? $options['verseOpenTag'] : '';
		$options['verseCloseTag'] = isset($options['verseCloseTag']) ? $options['verseCloseTag'] : '';
		$options['verseEndTag'] = isset($options['verseEndTag']) ? $options['verseEndTag'] : '';
		$options['verseTag'] = $options['verseOpenTag'] . $options['verseCloseTag'];
		$options['chorusOpenTag'] = isset($options['chorusOpenTag']) ? $options['chorusOpenTag'] : '';
		$options['chorusEndTag'] = isset($options['chorusEndTag']) ? $options['chorusEndTag'] : '';
		$options['chordOpenTag'] = isset($options['chordOpenTag']) ? $options['chordOpenTag'] : '';
		$options['chordCloseTag'] = isset($options['chordCloseTag']) ? $options['chordCloseTag'] : '';
		$options['chordLineOpenTag'] = isset($options['chordLineOpenTag']) ? $options['chordLineOpenTag'] : '';
		$options['chordLineCloseTag'] = isset($options['chordLineCloseTag']) ? $options['chordLineCloseTag'] : '';
		$options['endOfLine'] = isset($options['endOfLine']) ? $options['endOfLine'] : '';
		$options['hellip'] = isset($options['hellip']) ? $options['hellip'] : '';

		$pattern[] = '/<[v|s]>/';
		$pattern[] = '/<(ch|r)>/';
		$pattern[] = '/<([a-zA-Z1-9#]+[\/]?[a-zA-Z1-9#]*)>/';
		$replacement[] = $options['verseTag'];
		$replacement[] = $options['chorusOpenTag'];
		$replacement[] = $options['chordOpenTag'] . '$1' . $options['chordCloseTag'];
		$markuped = nl2br(preg_replace($pattern, $replacement, $text));

		$line_list = explode('<br />', $markuped);

		$i = 0;
		$verse = 0;
		$isOpenedVerse = false;
		$isOpenedChorus = false;
		foreach ($line_list as $line) {

			// Remove all '<br />' tags
			$line_list[$i] = str_replace('<br />', '', $line_list[$i]);

			// Check if is some chords in line
			if (strpos($line_list[$i], $options['chordOpenTag']) !== false) {
				// Wrap line with chords
				$line_list[$i] = $options['chordLineOpenTag'] . $line_list[$i] . $options['chordLineCloseTag'];
			} elseif (!empty(trim($line_list[$i]))&&!(strpos($options['verseCloseTag'], trim($line_list[$i]))!==false)&&!(strpos($options['verseOpenTag'], trim($line_list[$i]))!==false)&&!(strpos($options['chorusOpenTag'], trim($line_list[$i]))!==false)) {
				// Break line without chords
				$line_list[$i] .= $options['endOfLine'];
			}

			// Process verse tag
			if (strpos($line_list[$i], $options['verseTag']) !== false) {
				$line_list[$i] = str_replace($options['verseTag'], $options['verseOpenTag'] . ++$verse . '.' . $options['verseCloseTag'], $line_list[$i]);
				if ($isOpenedVerse) {
					$line_list[$i-1] .= $options['verseEndTag'];
				}
				if ($isOpenedChorus) {
					$line_list[$i-1] .= $options['chorusEndTag'];
					$isOpenedChorus = false;
				}
				$isOpenedVerse = true;
			}

			// Process chorus tag
			if (strpos($line_list[$i], $options['chorusOpenTag']) !== false) {
				// Check if is on line only chorus tag
				if (trim($line_list[$i]) !== $options['chorusOpenTag'] . $options['endOfLine']) {
					// Save first line of chorus
					$chorusLine = $line_list[$i];
					// Remove chords
					$chorusLine = preg_replace('#' . addcslashes($options['chordOpenTag'], '<>/"') . '([a-zA-Z1-9]*)' . addcslashes($options['chordCloseTag'], '<>/"') . '#', '', $chorusLine);
					$chorusLine = rtrim(rtrim($chorusLine, $options['chordLineCloseTag']), ',.') . $options['hellip'] . $options['chordLineCloseTag'];

					if ($isOpenedVerse) {
						$line_list[$i-1] .= $options['verseEndTag'];
						$isOpenedVerse = false;
					}

					if ($isOpenedChorus) {
						$line_list[$i-1] .= $options['chorusEndTag'];
					}

					$isOpenedChorus = true;

				} else {
					// Add first line of chorus
					if (!empty($chorusLine)) {
						$line_list[$i] = $chorusLine;
						
						if ($isOpenedVerse) {
							$line_list[$i-1] .= $options['verseEndTag'];
							$isOpenedVerse = false;
						}

						if ($isOpenedChorus) {
							$line_list[$i-1] .= $options['chorusEndTag'];
						}

						$isOpenedChorus = true;
					}
				}
			}

			$i++;
		}

		// Implode lines to one string
		$lyric = implode($line_list);

		if ($isOpenedChorus) {
			$lyric .= $options['chorusEndTag'];
		} elseif ($isOpenedVerse) {
			$lyric .= $options['verseEndTag'];
		}

		return $lyric;
	}

	/**
	 * Create HTML from markup
	 * @param  string Markup text
	 * @return string HTML text
	 */
	public function getLyricInHtml() 
	{
		$options['verseOpenTag'] = '<span class="chord-verse">';
		$options['verseCloseTag'] = '</span>';
		$options['chorusOpenTag'] = '<span class="chord-chorus">R:</span>';
		$options['chordOpenTag'] = '<span class="chord"><span>';
		$options['chordCloseTag'] = '</span></span>';
		$options['chordLineOpenTag'] = '<div class="chord-line">';
		$options['chordLineCloseTag'] = '</div>';
		$options['endOfLine'] = '<br>';
		$options['hellip'] = '&hellip;';

		return $this->markupParser($this->lyric, $options);
	}
	
}
