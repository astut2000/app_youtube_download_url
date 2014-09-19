<?php

function e()
{
	echo implode('', array_map('htmlspecialchars', func_get_args()));
}
