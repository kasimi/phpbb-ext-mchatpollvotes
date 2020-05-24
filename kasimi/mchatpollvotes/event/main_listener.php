<?php

/**
 *
 * mChat Poll Votes. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\mchatpollvotes\event;

use dmzx\mchat\core\notifications;
use dmzx\mchat\core\settings;
use phpbb\event\data;
use phpbb\language\language;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	/** @var language */
	protected $language;

	/** @var notifications */
	protected $mchat_notifications;

	/** @var settings */
	protected $mchat_settings;

	public function __construct(
		language $language,
		notifications $mchat_notifications = null,
		settings $mchat_settings = null
	)
	{
		$this->language				= $language;
		$this->mchat_notifications	= $mchat_notifications;
		$this->mchat_settings		= $mchat_settings;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup'							=> 'user_setup',
			'core.viewtopic_modify_poll_ajax_data'		=> 'viewtopic_modify_poll_ajax_data',
			'dmzx.mchat.insert_posting_before'			=> 'mchat_insert_posting_before',
			'dmzx.mchat.process_notifications_before'	=> 'mchat_process_notifications_before',
			'dmzx.mchat.ucp_modify_template_data'		=> 'mchat_ucp_modify_template_data',
			'dmzx.mchat.global_settings_modify'			=> 'mchat_global_settings_modify',
		];
	}

	/**
	 * @param data $event
	 */
	public function user_setup(data $event)
	{
		if ($this->mchat_notifications)
		{
			$lang_set_ext = $event['lang_set_ext'];
			$lang_set_ext[] = [
				'ext_name' => 'kasimi/mchatpollvotes',
				'lang_set' => 'common',
			];
			$event['lang_set_ext'] = $lang_set_ext;
		}
	}

	/**
	 * @param data $event
	 */
	public function viewtopic_modify_poll_ajax_data(data $event)
	{
		if ($this->mchat_notifications)
		{
			$this->mchat_notifications->insert_post('vote', $event['topic_data']['forum_id'], $event['topic_data']['topic_first_post_id']);
		}
	}

	/**
	 * @param data $event
	 */
	public function mchat_insert_posting_before(data $event)
	{
		if ($event['mode'] == 'vote' && $this->mchat_settings->cfg('mchat_posts_vote'))
		{
			$event['is_mode_enabled'] = true;
		}
	}

	/**
	 * @param data $event
	 */
	public function mchat_process_notifications_before(data $event)
	{
		$event['notification_lang'] = array_merge($event['notification_lang'], [
			'MCHAT_NEW_VOTE',
		]);
	}

	/**
	 * @param data $event
	 */
	public function mchat_ucp_modify_template_data(data $event)
	{
		if ($this->mchat_settings->cfg('mchat_posts_vote'))
		{
			$event->update_subarray('template_data', 'MCHAT_POSTS_ENABLED_LANG', implode($this->language->lang('COMMA_SEPARATOR'), [
				$event['template_data']['MCHAT_POSTS_ENABLED_LANG'],
				$this->language->lang('MCHAT_POSTS_VOTE'),
			]));
		}
	}

	/**
	 * @param data $event
	 */
	public function mchat_global_settings_modify(data $event)
	{
		$event['global_settings'] = array_merge($event['global_settings'], [
			'mchat_posts_vote' => ['default' => 0],
		]);
	}
}
