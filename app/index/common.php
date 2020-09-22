<?php

/**
 * 获取mobile域的session
 * @param $name
 * @return mixed
 */
function getSession($name)
{
    return session($name, '', config('prefix'));
}