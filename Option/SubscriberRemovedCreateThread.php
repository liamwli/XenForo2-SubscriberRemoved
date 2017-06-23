<?php

namespace SV\SubscriberRemoved\Option;

use XF\Option\AbstractOption;

class SubscriberRemovedCreateThread extends AbstractOption
{
	public static function renderOption(\XF\Entity\Option $option, array $htmlParams)
	{
		return self::getTemplate('sv_subscriberremoved_option_template_thread_data', $option, $htmlParams, [
			'nodes' => self::getSelectNodeChoices()
		]);
	}

	public static function verifyOption(array &$threadData, \XF\Entity\Option $option)
	{
		if ($threadData['create_thread'])
		{
			$threadAuthor = \XF::finder('XF:User')->where('username', $threadData['thread_author'])->fetchOne();

			if (!$threadAuthor)
			{
				$option->error(\XF::phrase('sv_subscriberremoved_invalid_thread_author'));

				return false;
			}
		}

		return true;
	}

	protected static function getSelectNodeChoices()
	{
		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = \XF::repository('XF:Node');

		$choices = $nodeRepo->getNodeOptionsData(true, 'Forum', 'option');
		$choices = array_map(function ($v)
		{
			$v['label'] = \XF::escapeString($v['label']);

			return $v;
		}, $choices);

		return $choices;
	}

}