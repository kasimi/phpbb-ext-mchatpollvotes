<?php

/**
 *
 * mChat Poll Votes. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\mchatpollvotes\migrations;

use phpbb\db\migration\migration;

class m1_initial_data extends migration
{
	/**
	 * @return array
	 */
	public function update_data()
	{
		return [
			['config.add', ['mchat_posts_vote', 0]],
		];
	}
}
