<?php
// logout.php
require 'config.php';
session_destroy();
jsonResponse(['success'=>true]);
