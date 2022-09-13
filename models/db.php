<?php
# load config yml file
$config = yaml_parse_file('../config.yml');

# connect to database mysqli
$db = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database']);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
