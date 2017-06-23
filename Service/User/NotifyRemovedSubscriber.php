<?php

namespace SV\SubscriberRemoved\Service\User;

use XF\Entity\User;
use XF\Service\AbstractService;

class NotifyRemovedSubscriber extends AbstractService
{
	protected $type;

	protected $sendConversation = false;
	protected $startThread = false;

	/** @var \XF\Entity\Forum */
	protected $threadForum = null;

	/** @var User */
	protected $threadAuthor = null;

	/** @var null|\XF\Entity\User */
	protected $removedSubscriber = null;

	protected $isSubscriber = null;

	/** @var  \XF\Entity\UserUpgrade[] */
	protected $activeUpgrades = null;

	public function __construct(\XF\App $app, User $removedSubscriber, $type = 'banned')
	{
		parent::__construct($app);

		$this->type = $type;
		$this->removedSubscriber = $removedSubscriber;
	}

	protected function setup()
	{
		$this->startThread = \XF::options()->subnotify_createthread;
		$this->sendConversation = \XF::options()->subnotify_sendpm;

		$this->setThreadData(\XF::options()->subnotify_thread_data);

		if ($this->isSubscriber === null || $this->activeUpgrades === null)
		{
			$this->isSubscriber = $this->determineIfSubscriber();
		}
	}

	protected function determineIfSubscriber()
	{
		$userUpgradeRepo = $this->repository('XF:UserUpgrade');
		$this->activeUpgrades = $userUpgradeRepo->findActiveUserUpgradesForList()
			->where('user_id', $this->removedSubscriber->user_id)->fetch();

		$this->isSubscriber = $this->activeUpgrades->count() > 0;
	}

	protected function setThreadData(array $threadData)
	{
		$this->threadForum = $this->findOne('XF:Forum', $threadData['node_id']);
		$this->threadAuthor = $this->finder('XF:User')->where('username', $threadData['thread_author'])->fetchOne();
	}

	public function notify()
	{

	}
}