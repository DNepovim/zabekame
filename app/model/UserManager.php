<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
class UserManager implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	const
		TABLE_NAME = 'zabe_users',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'username',
		COLUMN_FIRSTNAME = 'firstname',
		COLUMN_SURNAME = 'surname',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_EMAIL = 'mail';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update([
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			]); }

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $arr);
	}


	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return void
	 * @throws DuplicateNameException
	 */
	public function add($username, $firstname, $surname, $email, $password)
	{
		try {
			$this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_NAME => $username,
				self::COLUMN_FIRSTNAME => $firstname,
				self::COLUMN_SURNAME => $surname,
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
				self::COLUMN_EMAIL => $email,
			]);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

	public function getIDByNick($username)
	{
		return $this->database->table(self::TABLE_NAME )->select(self::COLUMN_ID)->where(self::COLUMN_NAME, $username)->fetchField(self::COLUMN_ID);
	}

	public function getNickByID($userid)
	{
		return $this->database->table(self::TABLE_NAME )->select(self::COLUMN_NAME)->get($userid)->username;
	}

	public function existUsername($username)
	{
		return (bool)$this->database->table(self::TABLE_NAME )->where(self::COLUMN_NAME, $username)->fetch();
	}
}



class DuplicateNameException extends \Exception
{}
