<?php

class NotableMembers_Listener
{
    public static function load_class($class, array &$extend)
    {
        static $classes = [
            'XenForo_ControllerPublic_Member',
        ];

        if (in_array($class, $classes)) {
            $extend[] = 'NotableMembers_' . $class;
        }
    }
}
