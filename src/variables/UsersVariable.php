<?php
namespace Blocks;

/**
 * User functions
 */
class UsersVariable
{
	/**
	 * Returns the current logged-in user.
	 * @return User
	 */
	public function current()
	{
		return blx()->users->getCurrentUser();
	}

	/**
	 * Returns a user by its ID.
	 * @param $userId
	 * @return User
	 */
	public function getById($userId)
	{
		return blx()->users->getUserById($userId);
	}

	/**
	 * Gets a user by a verification code.
	 *
	 * @param string $code
	 * @return User
	 */
	public function getUserByVerificationCode($code)
	{
		return blx()->users->getUserByVerificationCode($code);
	}

	/**
	 * Returns the recent users.
	 * @return array
	 */
	public function recent()
	{
		return blx()->users->getRecentUsers();
	}

	/**
	 * Returns the URL segment for account verification.
	 * @return string
	 */
	public function verifyAccountUrl()
	{
		return blx()->users->getVerifyAccountUrl();
	}
}
