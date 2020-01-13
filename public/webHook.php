<?php
$pwd = getcwd();
$command = 'cd ' . $pwd . ' && git pull';
$output = shell_exec($command);
print $output;