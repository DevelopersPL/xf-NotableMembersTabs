<?php

class NotableMembers_XenForo_ControllerPublic_Member extends XFCP_NotableMembers_XenForo_ControllerPublic_Member
{
    public function actionIndex()
    {
        $ret = parent::actionIndex();

        if ($ret instanceof XenForo_ControllerResponse_View) {
            $arr = [];
            $titles = explode(',', XenForo_Application::getOptions()->NotableMembersTitles);
            $slugs = explode(',', XenForo_Application::getOptions()->NotableMembersTitleSlugs);
            foreach ($titles as $key => $title) {
                $arr[$slugs[$key]] = $title;
            }
            $ret->params['nm_add'] = $arr;
        }

        return $ret;
    }

    protected function _getNotableMembers($type, $limit)
    {
        $userModel = $this->_getUserModel();

        $notableCriteria = [
            'is_banned' => 0,
        ];
        $typeMap = [
            'messages' => 'message_count',
            'likes'    => 'like_count',
        ];

        $slugs = explode(',', XenForo_Application::getOptions()->NotableMembersTitleSlugs);
        foreach (explode(',', XenForo_Application::getOptions()->NotableMembersSortOrder) as $key => $sort) {
            $typeMap[$slugs[$key]] = $sort;
        }

        if (in_array($type, $slugs)) {
            $groups = explode(',', XenForo_Application::getOptions()->NotableMembersSecondaryGroups);
            $notableCriteria['secondary_group_ids'] = [$groups[array_search($type, $slugs)]];
        }

        if (XenForo_Application::getOptions()->enableTrophies) {
            $typeMap['points'] = 'trophy_points';
        }

        if (!isset($typeMap[$type])) {
            return false;
        }

        $field = $typeMap[$type];

        $notableCriteria[$field] = ['>', 0];

        return [$userModel->getUsers($notableCriteria, [
            'join'      => XenForo_Model_User::FETCH_USER_FULL,
            'limit'     => $limit,
            'order'     => $field,
            'direction' => 'desc',
        ]), $typeMap[$type]];
    }
}
