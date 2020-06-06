<?php
$cid = go(function () {
    $id = co::getuid();
    echo "start coro $id\n";
    co::suspend();
    echo "resume core $id @1\n";
    co::suspend();
    echo "resume core $id @2\n";
});
echo "start to resume $cid @1\n";
co::resume($cid);
echo "start to resume $cid @2\n";
co::resume($cid);
echo "main\n";