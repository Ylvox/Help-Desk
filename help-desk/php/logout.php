<?php

    session_name('helpdesk_rocio');
    session_start();
    session_unset();
    session_destroy();
    header('Location: ../restrito/');

?>