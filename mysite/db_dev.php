<?php
return array(
	"type" => 'SQLiteDatabase',
	"server" => '',
	"username" => '',
	"password" => '',
	"database" => $project.'.sqlite',
	"path" => realpath(dirname($_project_dir).'/assets/.db/'),
);
