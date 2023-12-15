<?php
session_start();
session_destroy();
// redirect user to main page
header("location: ../");
