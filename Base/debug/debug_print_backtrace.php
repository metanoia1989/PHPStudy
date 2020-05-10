<?php

function a() {
    b();
}

function b() {
    c();
}

function c() {
    // 查看程序的调用栈，方便清理程序执行的上下文环境
    debug_print_backtrace(); 
}

a();   