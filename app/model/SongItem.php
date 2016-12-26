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

	/** @var Nette\Database\Context */
	private $database;

	public $user;
	public $title;
	public $guid;
	public $interpreter;
	public $lyric;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Get sont data.
	 * @param  string  Song id or guid
	 * @return string HTML text
	 */
	public function getSong($id)
	{
		if (is_numeric($id)) {
			$song = $this->database->table( self::TABLE_NAME )->select('*')->get( $id );
		} else {
			$song = $this->database->table(self::TABLE_NAME )->select('*')->where(self::COLUMN_GUID, $id)->fetch();
		}

		if ($song) {

			$this->user = $song->user;
			$this->title = $song->title;
			$this->guid = $song->guid;
			$this->interpreter = $song->interpreter;
			$this->lyric = $this->markupParser($song->lyric);

			return true;

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

		$pattern[] = '/<[v]>/';
		$pattern[] = '/<([\/v]{2})>/';
		$pattern[] = '/<[ch]{2}>/';
		$pattern[] = '/<([\/ch]{3})>/';
		$pattern[] = '/<([a-zA-Z1-9]*)>/';
		$replacement[] = '<div class="verse">';
		$replacement[] = '</div>';
		$replacement[] = '<div class="chorus">';
		$replacement[] = '</div>';
		$replacement[] = '<span class="chord"><i>$1</i></span>';
		$markuped = nl2br(preg_replace($pattern, $replacement, $text));

		$line_list = explode('<br />', $markuped);

		$i = 0;
		foreach ($line_list as $line) {

			$line_list[$i] = str_replace('<br />', '', $line_list[$i]);

			if (strpos($line, '<span class="chord">')) {
				$line_list[$i] = '<p class="chord-line">' . $line_list[$i] . '</p>';
			} elseif (!preg_match('/^<\/div>$/', trim($line_list[$i]))&&!preg_match('/^<div class=\"(verse|chorus)\">$/', trim($line_list[$i]))) {
				$line_list[$i] .= '<br>';
			}
			$i++;
		}

		$lyric = implode($line_list);

		return $lyric;
	}

}
